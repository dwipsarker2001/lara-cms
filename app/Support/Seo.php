<?php

namespace App\Support;

use App\Http\Controllers\Admin\SeoController;

use App\Models\Setting;

/**
 * Class Seo
 * Helper class to dynamically compile and render SEO/meta HTML tags for public pages.
 */
class Seo
{
    /**
     * Render the full HTML block of SEO tags for a given page.
     */
    public static function render(mixed $page = null): string
    {
        $settings = Setting::firstOrCreate(['id' => 1]);

        // Retrieve and merge global SEO settings
        $globalSeo = SeoController::DEFAULT_SEO;
        if (is_array($settings->seo)) {
            foreach (SeoController::DEFAULT_SEO as $key => $default) {
                $value = $settings->seo[$key] ?? null;
                if ($value !== null) {
                    $globalSeo[$key] = $value;
                }
            }
        }

        // Get page-specific metadata
        $pageMeta = [];
        if ($page && is_array($page->meta)) {
            $pageMeta = $page->meta;
        }

        $html = [];

        // 1. Title Tag
        $pageTitle = trim($pageMeta['metaTitle'] ?? '');
        if ($pageTitle === '' && $page) {
            $pageTitle = $page->title;
        }
        if ($pageTitle === '') {
            $pageTitle = config('app.name', 'Lara CMS');
        }

        $siteName = trim($globalSeo['siteName'] ?? '');
        $separator = trim($globalSeo['separator'] ?? '|');
        $namePosition = $globalSeo['namePosition'] ?? 'After';

        if ($siteName !== '' && $namePosition !== 'None') {
            if ($namePosition === 'Before') {
                $titleText = "{$siteName} {$separator} {$pageTitle}";
            } else {
                $titleText = "{$pageTitle} {$separator} {$siteName}";
            }
        } else {
            $titleText = $pageTitle;
        }
        $html[] = '<title>'.e($titleText).'</title>';

        // 2. Meta Description
        $metaDescription = trim($pageMeta['metaDescription'] ?? '');
        if ($metaDescription === '') {
            $metaDescription = trim($globalSeo['metaDescription'] ?? '');
        }
        if ($metaDescription !== '') {
            $html[] = '<meta name="description" content="'.e($metaDescription).'">';
        }

        // 3. Robots Tags
        $robotsDirectives = [];

        // Main indexing and follow
        $robotsValue = $pageMeta['robots'] ?? 'Inherit';
        if ($robotsValue !== 'Inherit') {
            $robotsDirectives[] = $robotsValue;
        } else {
            // Indexing
            $indexing = $pageMeta['indexing'] ?? 'Inherit';
            if ($indexing === 'Inherit') {
                $robotsDirectives[] = ($globalSeo['indexing'] ?? true) ? 'index' : 'noindex';
            } else {
                $robotsDirectives[] = ($indexing === 'Yes') ? 'index' : 'noindex';
            }

            // Link following
            $linkFollowing = $pageMeta['linkFollowing'] ?? 'Inherit';
            if ($linkFollowing === 'Inherit') {
                $robotsDirectives[] = ($globalSeo['linkFollowing'] ?? true) ? 'follow' : 'nofollow';
            } else {
                $robotsDirectives[] = ($linkFollowing === 'Yes') ? 'follow' : 'nofollow';
            }
        }

        // Additional robots options
        $additionalRobots = [
            'noArchive' => 'noarchive',
            'noImageIndex' => 'noimageindex',
            'noSnippet' => 'nosnippet',
            'noTranslate' => 'notranslate',
            'noSiteLinksSearchBox' => 'nositelinkssearchbox',
        ];

        foreach ($additionalRobots as $key => $directive) {
            $val = $pageMeta[$key] ?? 'Inherit';
            if ($val === 'Inherit') {
                if (! empty($globalSeo[$key])) {
                    $robotsDirectives[] = $directive;
                }
            } elseif ($val === 'Yes') {
                $robotsDirectives[] = $directive;
            }
        }

        // Snippet limits
        $maxSnippet = trim($pageMeta['maxSnippet'] ?? '');
        if ($maxSnippet !== '') {
            $robotsDirectives[] = 'max-snippet:'.e($maxSnippet);
        }
        $maxVideoPreview = trim($pageMeta['maxVideoPreview'] ?? '');
        if ($maxVideoPreview !== '') {
            $robotsDirectives[] = 'max-video-preview:'.e($maxVideoPreview);
        }
        $maxImagePreview = $pageMeta['maxImagePreview'] ?? 'Inherit';
        if ($maxImagePreview !== 'Inherit' && $maxImagePreview !== '') {
            $robotsDirectives[] = 'max-image-preview:'.e($maxImagePreview);
        }

        $html[] = '<meta name="robots" content="'.e(implode(', ', array_filter($robotsDirectives))).'">';

        // 4. Canonical URL
        $canonicalUrl = trim($pageMeta['canonicalUrl'] ?? '');
        if ($canonicalUrl === '') {
            $canonicalUrl = request()->url();
        }
        $html[] = '<link rel="canonical" href="'.e($canonicalUrl).'">';

        // 5. Open Graph (OG) Tags
        $ogType = $pageMeta['ogType'] ?? 'Inherit';
        if ($ogType === 'Inherit') {
            $ogType = 'website';
        }
        $html[] = '<meta property="og:type" content="'.e($ogType).'">';

        $ogTitle = trim($pageMeta['ogTitle'] ?? '');
        if ($ogTitle === '') {
            $ogTitle = $pageTitle;
        }
        $html[] = '<meta property="og:title" content="'.e($ogTitle).'">';

        if ($metaDescription !== '') {
            $html[] = '<meta property="og:description" content="'.e($metaDescription).'">';
        }

        $socialImage = trim($pageMeta['socialImage'] ?? '');
        if ($socialImage === '') {
            $socialImage = trim($globalSeo['defaultSocialImage'] ?? '');
        }
        if ($socialImage !== '') {
            $html[] = '<meta property="og:image" content="'.e($socialImage).'">';
        }

        $ogSiteName = trim($globalSeo['ogSiteName'] ?? '');
        if ($ogSiteName === '') {
            $ogSiteName = $siteName;
        }
        if ($ogSiteName !== '') {
            $html[] = '<meta property="og:site_name" content="'.e($ogSiteName).'">';
        }

        $ogLocale = trim($globalSeo['ogLocale'] ?? 'en_US');
        $html[] = '<meta property="og:locale" content="'.e($ogLocale).'">';
        $html[] = '<meta property="og:url" content="'.e(request()->url()).'">';

        // 6. X (Twitter) Tags
        $xCard = $globalSeo['xCard'] ?? 'summary_large_image';
        $html[] = '<meta name="twitter:card" content="'.e($xCard).'">';

        $xHandle = trim($globalSeo['xHandle'] ?? '');
        if ($xHandle !== '') {
            $html[] = '<meta name="twitter:site" content="'.e($xHandle).'">';
        }

        $xCardTitle = trim($pageMeta['xCardTitle'] ?? '');
        if ($xCardTitle === '') {
            $xCardTitle = $pageTitle;
        }
        $html[] = '<meta name="twitter:title" content="'.e($xCardTitle).'">';

        $xCardDescription = trim($pageMeta['xCardDescription'] ?? '');
        if ($xCardDescription === '') {
            $xCardDescription = $metaDescription;
        }
        if ($xCardDescription !== '') {
            $html[] = '<meta name="twitter:description" content="'.e($xCardDescription).'">';
        }

        if ($socialImage !== '') {
            $html[] = '<meta name="twitter:image" content="'.e($socialImage).'">';
        }

        // 7. Schema Markup / JSON-LD
        $schema = trim($pageMeta['schema'] ?? '');
        if ($schema !== '') {
            $html[] = '<script type="application/ld+json">'.$schema.'</script>';
        }

        // 8. Custom / Search Engine meta tags
        if (! empty($globalSeo['searchEnginesEnabled'])) {
            $extraMetaTags = trim($globalSeo['extraMetaTags'] ?? '');
            if ($extraMetaTags !== '') {
                $html[] = $extraMetaTags;
            }
        }

        return implode("\n    ", $html);
    }
}
