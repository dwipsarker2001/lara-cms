<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Repository for Automatic Updates
    |--------------------------------------------------------------------------
    | Repository in "owner/repo" format. Used to fetch version.json from
    | raw.githubusercontent.com (CDN — no rate limits).
    */
    'github_repo' => env('CMS_GITHUB_REPO', 'dwipsarker2001/lara-cms'),

    /*
    |--------------------------------------------------------------------------
    | Repository Branch
    |--------------------------------------------------------------------------
    | The branch to read version.json from. Typically "main" or "master".
    */
    'github_branch' => env('CMS_GITHUB_BRANCH', 'main'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Download URL
    |--------------------------------------------------------------------------
    | Fallback download URL used if version.json doesn't specify one.
    */
    'update_url' => env('CMS_UPDATE_URL', 'https://github.com/dwipsarker2001/lara-cms/archive/refs/heads/main.zip'),
];
