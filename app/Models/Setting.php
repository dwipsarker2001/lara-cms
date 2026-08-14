<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'seo' => 'array',
        'payment' => 'array',
        'custom_fields' => 'array',
        'custom_values' => 'array',
    ];

    protected $fillable = [
        'theme_color',
        'currency',
        'language',
        'logo_light',
        'logo_dark',
        'contact_number',
        'admin_theme',
        'seo',
        'payment',
        'cms_version',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'custom_fields',
        'custom_values',
    ];

    public static function getCustom(string $key, mixed $default = null): mixed
    {
        $settings = static::first();
        if (! $settings || ! is_array($settings->custom_values)) {
            return $default;
        }

        return $settings->custom_values[$key] ?? $default;
    }

    public function getCustomValue(string $key, mixed $default = null): mixed
    {
        if (! is_array($this->custom_values)) {
            return $default;
        }

        return $this->custom_values[$key] ?? $default;
    }

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
