<?php

use App\Console\Commands\ScheduleCampaign;
use App\Console\Commands\SendReports;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('schedule:campaign', function () {
    $this->call(ScheduleCampaign::class);
})->purpose('Send scheduled campaigns');

Artisan::command('send:reports', function () {
    $this->call(SendReports::class);
})->purpose('Send weekly reports to users');
