<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CmsUpdaterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(
        protected CmsUpdaterService $updater
    ) {}

    /**
     * Fetch latest release info by reading version.json from raw.githubusercontent.com.
     *
     * @return array{version: string|null, download_url: string|null, source: string}
     */
    public function getLatestReleaseInfo(bool $forceRefresh = false): array
    {
        return $this->updater->getLatestReleaseInfo($forceRefresh);
    }

    /**
     * Check for available updates and return JSON status.
     */
    public function check(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('force') || $request->boolean('refresh');
        $result = $this->updater->check($forceRefresh);

        return response()->json($result);
    }

    /**
     * Execute the full update process.
     */
    public function run(): JsonResponse
    {
        $result = $this->updater->run();

        if (! ($result['success'] ?? false)) {
            $status = isset($result['error']) ? 500 : 200;

            return response()->json($result, $status);
        }

        return response()->json($result);
    }

    public function getCurrentVersion(?Setting $settings = null): string
    {
        return $this->updater->getCurrentVersion($settings);
    }
}
