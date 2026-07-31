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
        'logo_light',
        'logo_dark',
        'contact_number',
        'admin_theme',
        'seo',
        'payment',
        'cms_version',
    ];

    public static function getCurrencySymbol(): string
    {
        $code = static::first()?->currency ?? 'USD';

        return match (strtoupper($code)) {
            'EUR' => '€',
            'GBP' => '£',
            'BDT' => '৳',
            'INR' => '₹',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'JPY', 'CNY' => '¥',
            'SAR' => '﷼',
            'AED' => 'د.إ',
            default => '$',
        };
    }

    public static function getCurrencyCode(): string
    {
        return static::first()?->currency ?? 'USD';
    }
}
