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
        'ai_base_url',
        'ai_api_key',
        'ai_model',
        'unsplash_access_key',
        'pexels_api_key',
        'pixabay_api_key',
        'image_provider',
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

    public static function getSiteName(string $default = 'LaraCMS'): string
    {
        $setting = static::first();
        if ($setting && is_array($setting->seo) && ! empty($setting->seo['site_title'])) {
            return $setting->seo['site_title'];
        }

        return config('app.name', $default);
    }

    public static function getLogo(string $type = 'light'): ?string
    {
        $setting = static::first();
        if (! $setting) {
            return null;
        }

        return $type === 'dark'
            ? ($setting->logo_dark ?? $setting->logo_light)
            : ($setting->logo_light ?? $setting->logo_dark);
    }

    public static function getContactNumber(): ?string
    {
        return static::first()?->contact_number;
    }

    public function getMaskedAiApiKey(): ?string
    {
        return $this->maskSecretKey($this->ai_api_key);
    }

    public function getMaskedUnsplashKey(): ?string
    {
        return $this->maskSecretKey($this->unsplash_access_key);
    }

    public function getMaskedPexelsKey(): ?string
    {
        return $this->maskSecretKey($this->pexels_api_key);
    }

    public function getMaskedPixabayKey(): ?string
    {
        return $this->maskSecretKey($this->pixabay_api_key);
    }

    protected function maskSecretKey(?string $key): ?string
    {
        if (empty($key)) {
            return null;
        }

        $len = strlen($key);

        if ($len <= 8) {
            return str_repeat('*', max(6, $len));
        }

        $prefix = substr($key, 0, 4);
        $suffix = substr($key, -4);
        $maskedLength = min(16, max(8, $len - 8));

        return $prefix.str_repeat('*', $maskedLength).$suffix;
    }
}
