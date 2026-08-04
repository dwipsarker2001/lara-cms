<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateController extends Controller
{
    /**
     * Fetch latest release info by reading version.json from raw.githubusercontent.com.
     *
     * This uses GitHub's raw content CDN which has NO rate limits (unlike
     * the GitHub API which caps at 60 req/hr unauthenticated). The version
     * info is read from the version.json file in the repository root.
     *
     * Returns an array with 'version', 'download_url', and 'source'.
     * The 'source' field indicates where the version came from:
     *   - 'remote'  — fetched from raw.githubusercontent.com
     *   - 'cache'   — served from cache
     *   - 'failed'  — remote was unreachable (no version could be determined)
     *
     * CRITICAL: Failed results are NEVER cached. Only successful
     * responses are cached, preventing stale data from masking real updates.
     *
     * @return array{version: string|null, download_url: string|null, source: string}
     */
    public function getLatestReleaseInfo(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('cms_latest_release_info');
        }

        // Return cached data if available (only successful results are ever cached)
        $cached = Cache::get('cms_latest_release_info');
        if ($cached !== null) {
            $cached['source'] = 'cache';

            return $cached;
        }

        $repo = config('cms.github_repo');

        if (empty($repo)) {
            return [
                'version' => null,
                'download_url' => null,
                'source' => 'failed',
            ];
        }

        $branch = config('cms.github_branch', 'main');

        try {
            // Fetch version.json from raw.githubusercontent.com (CDN — NO rate limits)
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'LaraCMS-Updater',
                    'Accept' => 'application/json',
                ])
                ->get("https://raw.githubusercontent.com/{$repo}/{$branch}/version.json");

            if ($response->successful()) {
                $data = $response->json();
                $version = ltrim($data['version'] ?? '', 'v');
                $downloadUrl = $data['download_url']
                    ?? "https://github.com/{$repo}/archive/refs/tags/v{$version}.zip";

                if (! empty($version)) {
                    $result = [
                        'version' => $version,
                        'download_url' => $downloadUrl,
                        'source' => 'remote',
                    ];

                    // Cache successful results for 30 minutes
                    Cache::put('cms_latest_release_info', $result, 1800);

                    return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Update check failed: {$e->getMessage()}");
        }

        // Failed — do NOT cache this
        return [
            'version' => null,
            'download_url' => null,
            'source' => 'failed',
        ];
    }

    /**
     * Check for available updates and return JSON status.
     *
     * Possible 'status' values:
     *   - 'update_available' — a newer version exists
     *   - 'up_to_date'      — running the latest version
     *   - 'check_failed'    — could not reach update server to verify
     */
    public function check(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('force') || $request->boolean('refresh');
        $settings = Setting::firstOrCreate(['id' => 1]);
        $current = $settings->cms_version ?? '1.0.0';
        $latestInfo = $this->getLatestReleaseInfo($forceRefresh);

        if ($latestInfo['source'] === 'failed') {
            return response()->json([
                'current_version' => $current,
                'latest_version' => null,
                'update_available' => false,
                'status' => 'check_failed',
                'message' => 'Unable to reach the update server. Please try again later or check your internet connection.',
            ]);
        }

        $latest = $latestInfo['version'];
        $updateAvailable = version_compare($latest, $current, '>');

        return response()->json([
            'current_version' => $current,
            'latest_version' => $latest,
            'update_available' => $updateAvailable,
            'status' => $updateAvailable ? 'update_available' : 'up_to_date',
        ]);
    }

    /**
     * Execute the full update process:
     * 1. Download the update ZIP from the resolved download URL
     * 2. Extract it over the base path (stripping top-level archive prefix if present)
     * 3. Run database migrations
     * 4. Clear system caches
     * 5. Persist the new version number
     */
    public function run(): JsonResponse
    {
        $settings = Setting::firstOrCreate(['id' => 1]);
        $current = $settings->cms_version ?? '1.0.0';
        $latestInfo = $this->getLatestReleaseInfo();
        $latest = $latestInfo['version'];
        $updateUrl = $latestInfo['download_url'];

        // Cannot run update if we couldn't determine the latest version
        if ($latestInfo['source'] === 'failed' || empty($latest) || empty($updateUrl)) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to determine the latest version. Please check your internet connection and try again.',
            ]);
        }

        if (! version_compare($latest, $current, '>')) {
            return response()->json([
                'success' => false,
                'message' => 'System is already up to date.',
            ]);
        }

        $logs = [];
        $tempZip = null;

        try {
            // Step 1 — Environment check
            if (! class_exists(ZipArchive::class)) {
                throw new \Exception('PHP ZipArchive extension is not available on this server.');
            }

            if (! is_writable(base_path())) {
                throw new \Exception('Base directory is not writable. Please check file permissions.');
            }

            $logs[] = '[1/4] Environment check passed — ZipArchive available, directory writable.';

            // Step 2 — Download package
            $logs[] = "Downloading update package v{$latest} from remote server...";

            $response = Http::timeout(120)
                ->withHeaders([
                    'Accept' => '*/*',
                    'User-Agent' => 'LaraCMS-Updater',
                ])
                ->get($updateUrl);

            if ($response->failed()) {
                throw new \Exception("Download failed (HTTP {$response->status()}). Please check the update URL.");
            }

            $tempZip = sys_get_temp_dir().DIRECTORY_SEPARATOR.'lara_cms_update_'.uniqid().'.zip';
            File::put($tempZip, $response->body());

            $sizeKb = round(File::size($tempZip) / 1024, 1);
            $logs[] = "[2/4] Downloaded update package ({$sizeKb} KB). Extracting files...";

            // Step 3 — Selective Extract ZIP (never overwrite protected directories)
            $zip = new ZipArchive;
            $openResult = $zip->open($tempZip);

            if ($openResult !== true) {
                throw new \Exception("Could not open the downloaded ZIP package (ZipArchive error code: {$openResult}).");
            }

            // Detect common root directory prefix in GitHub ZIPs (e.g. repo-tag/ or lara-cms-1.1.2/)
            $rootPrefix = '';
            if ($zip->count() > 0) {
                $firstEntry = str_replace('\\', '/', $zip->getNameIndex(0));
                if (str_contains($firstEntry, '/')) {
                    $potentialRoot = explode('/', $firstEntry)[0].'/';
                    $allHavePrefix = true;
                    for ($i = 0; $i < $zip->count(); $i++) {
                        $name = str_replace('\\', '/', $zip->getNameIndex($i));
                        if ($name !== '' && ! str_starts_with($name, $potentialRoot)) {
                            $allHavePrefix = false;
                            break;
                        }
                    }
                    if ($allHavePrefix) {
                        $rootPrefix = $potentialRoot;
                    }
                }
            }

            /**
             * Protected paths that must NEVER be overwritten by an update.
             * These contain user data, custom plugins, and environment config.
             */
            $protectedPrefixes = [
                'plugins/',                       // User's custom plugin tools
                'storage/',                       // Uploaded files, logs, cache
                '.env',                           // Environment configuration
                'app/Blocks/custom/',             // Custom user block classes
                'resources/views/blocks/custom/', // Custom user block templates
                'resources/views/public/',        // Custom public layout (fonts, head tags, etc.)
            ];

            $extracted = 0;
            $skipped = 0;

            for ($i = 0; $i < $zip->count(); $i++) {
                $entryName = str_replace('\\', '/', $zip->getNameIndex($i));
                $relativePath = $rootPrefix !== '' ? substr($entryName, strlen($rootPrefix)) : $entryName;

                if ($relativePath === '' || str_ends_with($relativePath, '/')) {
                    continue;
                }

                // Skip any entry that starts with a protected prefix
                $isProtected = false;
                foreach ($protectedPrefixes as $prefix) {
                    if (str_starts_with($relativePath, $prefix) || $relativePath === ltrim($prefix, '/')) {
                        $isProtected = true;
                        break;
                    }
                }

                if ($isProtected) {
                    $skipped++;

                    continue;
                }

                $targetPath = base_path($relativePath);
                File::ensureDirectoryExists(dirname($targetPath));

                $stream = $zip->getStream($entryName);
                if ($stream) {
                    file_put_contents($targetPath, stream_get_contents($stream));
                    fclose($stream);
                    $extracted++;
                }
            }

            $zip->close();
            File::delete($tempZip);
            $tempZip = null;

            $logs[] = "Extracted {$extracted} file(s) from the update package. Skipped {$skipped} protected file(s).";

            // Step 4 — Database migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output() ?: 'No pending migrations found.');
            $logs[] = "[3/4] Database migrations complete: {$migrateOutput}";

            // Step 5 — Clear caches
            Artisan::call('optimize:clear');
            Cache::forget('cms_latest_release_info');
            $logs[] = '[4/4] System cache and config cleared.';

            // Persist version
            $settings->update(['cms_version' => $latest]);
            $logs[] = "✓ Lara CMS successfully updated to v{$latest}!";

            Log::info("Lara CMS updated from v{$current} to v{$latest}.");

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'version' => $latest,
            ]);
        } catch (\Exception $e) {
            if ($tempZip && file_exists($tempZip)) {
                @unlink($tempZip);
            }

            $logs[] = '[ERROR] '.$e->getMessage();
            Log::error("Lara CMS update failed: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'logs' => $logs,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
