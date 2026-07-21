<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'seo' => 'array',
        'payment' => 'array',
    ];

    protected $fillable = [
        'site_title',
        'theme_color',
        'currency',
        'default_subscription_plan_id',
        'logo_light',
        'logo_dark',
        'contact_number',
        'admin_theme',
        'seo',
        'payment',
        'cms_version',
        'sendgrid_api_key',
        'sendgrid_from_email',
    ];

    public static function getSendGridApiKey(): ?string
    {
        $dbKey = static::first()?->sendgrid_api_key;

        return ! empty($dbKey) ? $dbKey : env('SENDGRID_APIKEY');
    }

    public static function getSendGridFromEmail(): ?string
    {
        $dbEmail = static::first()?->sendgrid_from_email;

        return ! empty($dbEmail) ? $dbEmail : env('MAIL_FROM_ADDRESS');
    }
}
