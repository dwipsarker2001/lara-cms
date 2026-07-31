<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateController extends Controller
{
    /**
     * Check for available updates and return JSON status.
     *
     * @return JsonResponse
     */
    public function check()
    {
        $settings = Setting::firstOrCreate(['id' => 1]);
        $current = $settings->cms_version ?? '1.0.0';
        $latest = config('cms.latest_version', '1.0.0');
        $updateAvailable = version_compare($latest, $current, '>');

        return response()->json([
            'current_version' => $current,
            'latest_version' => $latest,
            'update_available' => $updateAvailable,
        ]);
    }

    /**
     * Execute the full update process:
     * 1. Download the update ZIP from the configured URL
     * 2. Extract it over the base path
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
        $latest = config('cms.latest_version', '1.0.0');
        $updateUrl = config('cms.update_url');

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
            $logs[] = 'Downloading update package from remote server...';

            $response = Http::timeout(120)
                ->withHeaders(['Accept' => 'application/octet-stream'])
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

            /**
             * Protected paths that must NEVER be overwritten by an update.
             * These contain user data, custom plugins, and environment config.
             */
            $protectedPrefixes = [
                'plugins/',                  // User's custom plugin tools
                'storage/',                  // Uploaded files, logs, cache
                '.env',                      // Environment configuration
                'app/Blocks/Custom/',         // Custom user block classes
                'resources/views/blocks/custom/', // Custom user block templates
            ];

            $extracted = 0;
            $skipped = 0;

            for ($i = 0; $i < $zip->count(); $i++) {
                $entryName = $zip->getNameIndex($i);

                // Normalise Windows paths
                $entryName = str_replace('\\', '/', $entryName);

                // Skip any entry that starts with a protected prefix
                $isProtected = false;
                foreach ($protectedPrefixes as $prefix) {
                    if (str_starts_with($entryName, $prefix) || $entryName === ltrim($prefix, '/')) {
                        $isProtected = true;
                        break;
                    }
                }

                if ($isProtected) {
                    $skipped++;

                    continue;
                }

                $zip->extractTo(base_path(), [$entryName]);
                $extracted++;
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
