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
     * Fetch latest release info dynamically from GitHub API (or fallback to config).
     *
     * @return array{version: string, download_url: string}
     */
    public function getLatestReleaseInfo(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('cms_latest_release_info');
        }

        return Cache::remember('cms_latest_release_info', 1800, function () {
            $repo = config('cms.github_repo');
            $fallbackVersion = config('cms.latest_version', '1.0.0');
            $fallbackUrl = config('cms.update_url', "https://github.com/{$repo}/archive/refs/heads/main.zip");

            if (empty($repo)) {
                return [
                    'version' => $fallbackVersion,
                    'download_url' => $fallbackUrl,
                ];
            }

            try {
                // 1. Try GitHub Releases API first (latest official release)
                $response = Http::timeout(5)
                    ->withHeaders([
                        'User-Agent' => 'LaraCMS-Updater',
                        'Accept' => 'application/vnd.github+json',
                    ])
                    ->get("https://api.github.com/repos/{$repo}/releases/latest");

                if ($response->successful()) {
                    $data = $response->json();
                    $tagName = ltrim($data['tag_name'] ?? '', 'v');
                    $zipUrl = $data['zipball_url'] ?? "https://github.com/{$repo}/archive/refs/tags/v{$tagName}.zip";

                    if (! empty($tagName)) {
                        return [
                            'version' => $tagName,
                            'download_url' => $zipUrl,
                        ];
                    }
                }

                // 2. Fallback to GitHub Tags API if no formal Release object exists
                $tagsResponse = Http::timeout(5)
                    ->withHeaders([
                        'User-Agent' => 'LaraCMS-Updater',
                        'Accept' => 'application/vnd.github+json',
                    ])
                    ->get("https://api.github.com/repos/{$repo}/tags");

                if ($tagsResponse->successful()) {
                    $tags = $tagsResponse->json();
                    if (is_array($tags) && count($tags) > 0) {
                        $firstTag = $tags[0]['name'] ?? '';
                        $tagName = ltrim($firstTag, 'v');
                        $zipUrl = "https://github.com/{$repo}/archive/refs/tags/{$firstTag}.zip";

                        if (! empty($tagName)) {
                            return [
                                'version' => $tagName,
                                'download_url' => $zipUrl,
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("GitHub update check failed: {$e->getMessage()}");
            }

            return [
                'version' => $fallbackVersion,
                'download_url' => $fallbackUrl,
            ];
        });
    }

    /**
     * Check for available updates and return JSON status.
     *
     * @return JsonResponse
     */
    public function check(Request $request)
    {
        $forceRefresh = $request->boolean('force') || $request->boolean('refresh');
        $settings = Setting::firstOrCreate(['id' => 1]);
        $current = $settings->cms_version ?? '1.0.0';
        $latestInfo = $this->getLatestReleaseInfo($forceRefresh);
        $latest = $latestInfo['version'];
        $updateAvailable = version_compare($latest, $current, '>');

        return response()->json([
            'current_version' => $current,
            'latest_version' => $latest,
            'update_available' => $updateAvailable,
        ]);
    }

    /**
     * Execute the full update process:
     * 1. Download the update ZIP from the configured URL or dynamic GitHub release URL
     * 2. Extract it over the base path (stripping top-level archive prefix if present)
     * 3. Run database migrations
     * 4. Clear system caches
     * 5. Persist the new version number
     *
     * @return JsonResponse
     */
    public function run()
    {
        $settings = Setting::firstOrCreate(['id' => 1]);
        $current = $settings->cms_version ?? '1.0.0';
        $latestInfo = $this->getLatestReleaseInfo();
        $latest = $latestInfo['version'];
        $updateUrl = $latestInfo['download_url'];

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
                throw new \Exception("Download failed (HTTP {$response->status()}). Please check the update URL in config/cms.php.");
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
