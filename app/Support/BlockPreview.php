<?php

namespace App\Support;

use App\Blocks\BlockRegistry;

class BlockPreview
{
    /**
     * Generate responsive visibility CSS classes based on device visibility settings.
     * Breakpoints:
     * - Laptop:  >= 1024px (lg)
     * - Tablet:  768px - 1023px (md)
     * - Mobile:  < 768px (< md)
     */
    public static function getDeviceVisibilityClasses(mixed $devices): string
    {
        if (is_string($devices)) {
            $trimmed = trim($devices);
            if ($trimmed === '' || $trimmed === 'null' || $trimmed === 'undefined' || $trimmed === '[]' || $trimmed === '{}') {
                return '';
            }
            $decoded = json_decode($devices, true);
            if (is_array($decoded)) {
                $devices = $decoded;
            } else {
                return '';
            }
        }

        if (! is_array($devices) || empty($devices)) {
            return '';
        }

        // Check if list array format ['laptop', 'tablet', ...]
        if (array_is_list($devices)) {
            $isLaptop = in_array('laptop', $devices, true);
            $isTablet = in_array('tablet', $devices, true);
            $isMobile = in_array('mobile', $devices, true);
        } else {
            $hasLaptop = array_key_exists('laptop', $devices);
            $hasTablet = array_key_exists('tablet', $devices);
            $hasMobile = array_key_exists('mobile', $devices);

            // If none of the device keys exist, it is not configured -> show on all screens
            if (! $hasLaptop && ! $hasTablet && ! $hasMobile) {
                return '';
            }

            $isLaptop = $hasLaptop ? filter_var($devices['laptop'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            if ($isLaptop === null) {
                $isLaptop = true;
            }

            $isTablet = $hasTablet ? filter_var($devices['tablet'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            if ($isTablet === null) {
                $isTablet = true;
            }

            $isMobile = $hasMobile ? filter_var($devices['mobile'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            if ($isMobile === null) {
                $isMobile = true;
            }
        }

        // If all 3 are visible (default) or all 3 are false/unset -> always show on all screens
        if (($isLaptop && $isTablet && $isMobile) || (! $isLaptop && ! $isTablet && ! $isMobile)) {
            return '';
        }

        // 1 device visible only
        if ($isLaptop && ! $isTablet && ! $isMobile) {
            return 'hidden lg:block';
        }
        if (! $isLaptop && $isTablet && ! $isMobile) {
            return 'hidden md:block lg:hidden';
        }
        if (! $isLaptop && ! $isTablet && $isMobile) {
            return 'block md:hidden';
        }

        // 2 devices visible (1 hidden)
        if (! $isLaptop && $isTablet && $isMobile) {
            return 'lg:hidden';
        }
        if ($isLaptop && ! $isTablet && $isMobile) {
            return 'block md:hidden lg:block';
        }
        if ($isLaptop && $isTablet && ! $isMobile) {
            return 'hidden md:block';
        }

        return '';
    }

    public static function render(array $sections, bool $withGlobals = true, mixed $page = null, bool $isEditor = false): string
    {
        $resolved = $withGlobals ? Sections::withGlobals($sections) : $sections;
        $registry = app(BlockRegistry::class);
        $html = '';

        foreach ($resolved as $i => $section) {
            $block = $registry->get($section['name'] ?? '');

            if (($section['enabled'] ?? true) === false || ! $block) {
                continue;
            }

            $inner = $block->render(
                data: $section['data'] ?? [],
                _key: $section['_key'] ?? '',
                preview: $isEditor,
                page: $page,
            );

            $devicesData = $section['data']['configuration']['devices']
                ?? $section['data']['devices']
                ?? $section['data']['background']['devices']
                ?? null;
            $deviceClasses = self::getDeviceVisibilityClasses($devicesData);
            $classes = array_filter([$deviceClasses, $isEditor ? 'p-0.5' : '']);
            $classAttr = ! empty($classes) ? ' class="'.implode(' ', $classes).'"' : '';

            $html .= '<div data-section-index="'.$i.'"'.$classAttr.'>'.$inner.'</div>';
        }

        return $html;
    }
}
