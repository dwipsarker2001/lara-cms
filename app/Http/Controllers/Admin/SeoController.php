<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * Site-wide SEO defaults, mirrored from the eTravel Pro SEO Pro page.
     * Stored as JSON on the settings.seo column and merged onto these
     * defaults so missing keys never reach a controlled input.
     */
    public const DEFAULT_SEO = [
        // Meta
        'metaDescription' => '',
        'siteName' => '',
        'namePosition' => 'After',
        'separator' => '|',
        // Robots
        'indexing' => true,
        'linkFollowing' => true,
        'noArchive' => false,
        'noImageIndex' => false,
        'noSnippet' => false,
        // Open Graph
        'ogSiteName' => '',
        'ogLocale' => 'en_US',
        'defaultSocialImage' => '',
        // X (Twitter)
        'xHandle' => '@',
        'xCard' => 'summary_large_image',
        // Sitemap
        'sitemapEnabled' => true,
        'sitemapChangeFrequency' => 'Monthly',
        'sitemapPriority' => '0.5',
        'sitemapLimit' => '1000',
        // Search engines
        'searchEnginesEnabled' => true,
        'searchEnginesIndexing' => true,
        'extraMetaTags' => '',
    ];

    public function index()
    {
        $settings = Setting::firstOrCreate(['id' => 1]);
        $seo = $this->mergeSeo($settings->seo);

        return view('admin.settings.seo', ['seo' => $seo]);
    }

    public function update(Request $request)
    {
        $data = $this->validateSeo($request);

        $settings = Setting::firstOrCreate(['id' => 1]);
        $settings->seo = $data;
        $settings->save();

        return back()->with('success', 'SEO settings updated.');
    }

    /**
     * Merge a stored (possibly partial) SEO array onto the defaults. Null
     * values are ignored so the default is used (a stored null must never
     * reach a controlled input).
     *
     * @param  array<string,mixed>|null  $raw
     * @return array<string,mixed>
     */
    private function mergeSeo(?array $raw): array
    {
        $result = self::DEFAULT_SEO;

        if ($raw === null) {
            return $result;
        }

        foreach (self::DEFAULT_SEO as $key => $default) {
            $value = $raw[$key] ?? null;

            if ($value !== null && $value !== '') {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Validate the SEO payload, casting booleans and strings to the shapes
     * expected by the SeoDefaults contract.
     *
     * @return array<string,mixed>
     */
    private function validateSeo(Request $request): array
    {
        $data = $request->validate([
            'metaDescription' => 'nullable|string|max:1000',
            'siteName' => 'nullable|string|max:255',
            'namePosition' => 'required|string|in:Before,After,None',
            'separator' => 'nullable|string|max:10',

            'indexing' => 'boolean',
            'linkFollowing' => 'boolean',
            'noArchive' => 'boolean',
            'noImageIndex' => 'boolean',
            'noSnippet' => 'boolean',

            'ogSiteName' => 'nullable|string|max:255',
            'ogLocale' => 'nullable|string|max:10',
            'defaultSocialImage' => 'nullable|string|max:255',

            'xHandle' => 'nullable|string|max:50',
            'xCard' => 'required|string|in:summary,summary_large_image,app,player',

            'sitemapEnabled' => 'boolean',
            'sitemapChangeFrequency' => 'required|string|in:Always,Hourly,Daily,Weekly,Monthly,Yearly,Never',
            'sitemapPriority' => 'nullable|string|max:5',
            'sitemapLimit' => 'nullable|string|max:10',

            'searchEnginesEnabled' => 'boolean',
            'searchEnginesIndexing' => 'boolean',
            'extraMetaTags' => 'nullable|string|max:5000',
        ]);

        // Coerce checkbox booleans (absent => false) so stored JSON is explicit.
        foreach (['indexing', 'linkFollowing', 'noArchive', 'noImageIndex', 'noSnippet', 'sitemapEnabled', 'searchEnginesEnabled', 'searchEnginesIndexing'] as $bool) {
            $data[$bool] = filter_var($data[$bool] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        return $data;
    }
}
