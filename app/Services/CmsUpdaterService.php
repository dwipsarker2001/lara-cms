<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class CmsUpdaterService
{
    /**
     * Fetch latest release info by reading version.json from raw.githubusercontent.com.
     *
     * @return array{version: string|null, download_url: string|null, source: string}
     */
    public function getLatestReleaseInfo(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            try {
                Cache::forget('cms_latest_release_info');
            } catch (\Throwable) {
            }
        }

        try {
            $cached = Cache::get('cms_latest_release_info');
            if ($cached !== null) {
                $cached['source'] = 'cache';

                return $cached;
            }
        } catch (\Throwable) {
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
            $response = Http::timeout(10)
                ->connectTimeout(5)
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

                    try {
                        Cache::put('cms_latest_release_info', $result, 1800);
                    } catch (\Throwable) {
                    }

                    return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Update check failed: {$e->getMessage()}");
        }

        return [
            'version' => null,
            'download_url' => null,
            'source' => 'failed',
        ];
    }

    /**
     * Check update status.
     *
     * @return array{current_version: string, latest_version: string|null, update_available: bool, status: string, message?: string}
     */
    public function check(bool $forceRefresh = false): array
    {
        $current = $this->getCurrentVersion();
        $latestInfo = $this->getLatestReleaseInfo($forceRefresh);

        if ($latestInfo['source'] === 'failed') {
            return [
                'current_version' => $current,
                'latest_version' => null,
                'update_available' => false,
                'status' => 'check_failed',
                'message' => 'Unable to reach the update server. Please try again later or check your internet connection.',
            ];
        }

        $latest = $latestInfo['version'];
        $updateAvailable = version_compare($latest, $current, '>');

        return [
            'current_version' => $current,
            'latest_version' => $latest,
            'update_available' => $updateAvailable,
            'status' => $updateAvailable ? 'update_available' : 'up_to_date',
        ];
    }

    /**
     * Execute update package download, extraction, migration, and optimization.
     *
     * @return array{success: bool, logs: array<string>, version?: string, error?: string, message?: string}
     */
    public function run(?callable $logger = null, bool $force = false): array
    {
        $logs = [];
        $log = function (string $msg) use ($logger, &$logs): void {
            $logs[] = $msg;
            if ($logger) {
                $logger($msg);
            }
        };

        $current = $this->getCurrentVersion();
        $latestInfo = $this->getLatestReleaseInfo(true);
        $latest = $latestInfo['version'];
        $updateUrl = $latestInfo['download_url'];

        if ($latestInfo['source'] === 'failed' || empty($latest) || empty($updateUrl)) {
            $msg = 'Unable to determine the latest version. Please check your internet connection and try again.';
            $log('[ERROR] '.$msg);

            return [
                'success' => false,
                'logs' => $logs,
                'message' => $msg,
            ];
        }

        if (! $force && ! version_compare($latest, $current, '>')) {
            $msg = 'System is already up to date.';
            $log($msg);

            return [
                'success' => false,
                'logs' => $logs,
                'message' => $msg,
            ];
        }

        $tempZip = null;

        try {
            // Step 1 — Environment check
            if (! class_exists(ZipArchive::class)) {
                throw new \Exception('PHP ZipArchive extension is not available on this server.');
            }

            if (! is_writable(base_path())) {
                throw new \Exception('Base directory is not writable. Please check file permissions.');
            }

            $log('[1/4] Environment check passed — ZipArchive available, directory writable.');

            // Step 2 — Download package
            $log("Downloading update package v{$latest} from remote server...");

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
            $log("[2/4] Downloaded update package ({$sizeKb} KB). Extracting files...");

            // Step 3 — Selective Extract ZIP (never overwrite protected directories)
            $zip = new ZipArchive;
            $openResult = $zip->open($tempZip);

            if ($openResult !== true) {
                throw new \Exception("Could not open the downloaded ZIP package (ZipArchive error code: {$openResult}).");
            }

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

            $log("Extracted {$extracted} file(s) from the update package. Skipped {$skipped} protected file(s).");

            // Step 4 — Database migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output() ?: 'No pending migrations found.');
            $log("[3/4] Database migrations complete: {$migrateOutput}");

            // Step 5 — Clear caches
            Artisan::call('optimize:clear');
            Cache::forget('cms_latest_release_info');
            $log('[4/4] System cache and config cleared.');

            // Persist version
            try {
                $settings = Setting::firstOrCreate(['id' => 1]);
                $settings->update(['cms_version' => $latest]);
            } catch (\Throwable) {
            }

            $log("✓ Lara CMS successfully updated to v{$latest}!");

            Log::info("Lara CMS updated from v{$current} to v{$latest}.");

            return [
                'success' => true,
                'logs' => $logs,
                'version' => $latest,
            ];
        } catch (\Exception $e) {
            if ($tempZip && file_exists($tempZip)) {
                @unlink($tempZip);
            }

            $log('[ERROR] '.$e->getMessage());
            Log::error("Lara CMS update failed: {$e->getMessage()}");

            return [
                'success' => false,
                'logs' => $logs,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getCurrentVersion(?Setting $settings = null): string
    {
        if (app()->runningUnitTests()) {
            try {
                $settings = $settings ?? Setting::first();
                if ($settings?->cms_version) {
                    return $settings->cms_version;
                }
            } catch (\Throwable) {
            }
        }

        $versionFile = base_path('version.json');
        if (file_exists($versionFile)) {
            $content = json_decode(@file_get_contents($versionFile), true);
            if (! empty($content['version'])) {
                return (string) $content['version'];
            }
        }

        try {
            $settings = $settings ?? Setting::first();
            if ($settings?->cms_version) {
                return $settings->cms_version;
            }
        } catch (\Throwable) {
        }

        return '1.0.0';
    }
}
