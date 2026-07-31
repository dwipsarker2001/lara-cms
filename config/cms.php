<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Repository for Automatic Updates
    |--------------------------------------------------------------------------
    | Repository in "owner/repo" format used to query GitHub Releases API
    | for the latest release tag and download package dynamically.
    | Set to null or empty to disable dynamic GitHub API lookups.
    */
    'github_repo' => env('CMS_GITHUB_REPO', 'dwipsarker2001/lara-cms'),

    /*
    |--------------------------------------------------------------------------
    | Fallback CMS Version & Download URL
    |--------------------------------------------------------------------------
    | Fallback values used if GitHub API is unreachable or disabled.
    */
    'latest_version' => env('CMS_LATEST_VERSION', '1.1.2'),
    'update_url' => env('CMS_UPDATE_URL', 'https://github.com/dwipsarker2001/lara-cms/archive/refs/heads/main.zip'),
];
