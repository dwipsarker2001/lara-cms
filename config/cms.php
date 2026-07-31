<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Version
    |--------------------------------------------------------------------------
    | The current latest version available for download. When this is greater
    | than the stored cms_version in the settings table, an update is offered
    | to the administrator.
    */
    'latest_version' => env('CMS_LATEST_VERSION', '1.1.2'),

    /*
    |--------------------------------------------------------------------------
    | Update Package URL
    |--------------------------------------------------------------------------
    | The URL from which the update ZIP archive is downloaded. This should
    | point to a release artifact (e.g., a GitHub release ZIP). In production,
    | replace this with your actual distribution URL.
    */
    'update_url' => env('CMS_UPDATE_URL', 'https://github.com/dwipsarker2001/lara-cms/archive/refs/heads/main.zip'),
];
