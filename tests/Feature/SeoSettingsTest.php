<?php

use App\Http\Controllers\Admin\SeoController;
use App\Models\Admin;
use App\Models\Setting;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('shows the SEO settings page with all six tabs', function () {
    get(route('admin.seo'))
        ->assertSuccessful()
        ->assertSee('Site Defaults')
        ->assertSee('Meta Data')
        ->assertSee('Robots Meta')
        ->assertSee('Open Graph')
        ->assertSee('Social Media')
        ->assertSee('Sitemap')
        ->assertSee('Search Engines');
});

it('redirects guests away from the SEO page', function () {
    auth('admin')->logout();

    get(route('admin.seo'))->assertRedirect();
});

it('persists SEO settings to the settings table', function () {
    $payload = [
        'metaDescription' => 'A travel CMS for explorers.',
        'siteName' => 'Lara CMS',
        'favicon' => '/uploads/favicon.png',
        'namePosition' => 'After',
        'separator' => '|',
        'indexing' => true,
        'linkFollowing' => true,
        'noArchive' => false,
        'noImageIndex' => false,
        'noSnippet' => false,
        'ogSiteName' => 'Lara CMS',
        'ogLocale' => 'en_US',
        'defaultSocialImage' => '/assets/og.jpg',
        'xHandle' => '@laracms',
        'xCard' => 'summary_large_image',
        'sitemapEnabled' => true,
        'sitemapChangeFrequency' => 'Weekly',
        'sitemapPriority' => '0.7',
        'sitemapLimit' => '2000',
        'searchEnginesEnabled' => true,
        'searchEnginesIndexing' => true,
        'extraMetaTags' => '<meta name="google-site-verification" content="abc" />',
    ];

    put(route('admin.seo.update'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success', 'SEO settings updated.');

    $seo = Setting::firstOrCreate(['id' => 1])->seo;

    expect($seo['metaDescription'])->toBe('A travel CMS for explorers.')
        ->and($seo['siteName'])->toBe('Lara CMS')
        ->and($seo['favicon'])->toBe('/uploads/favicon.png')
        ->and($seo['namePosition'])->toBe('After')
        ->and($seo['separator'])->toBe('|')
        ->and($seo['indexing'])->toBeTrue()
        ->and($seo['linkFollowing'])->toBeTrue()
        ->and($seo['noArchive'])->toBeFalse()
        ->and($seo['noImageIndex'])->toBeFalse()
        ->and($seo['noSnippet'])->toBeFalse()
        ->and($seo['ogSiteName'])->toBe('Lara CMS')
        ->and($seo['ogLocale'])->toBe('en_US')
        ->and($seo['defaultSocialImage'])->toBe('/assets/og.jpg')
        ->and($seo['xHandle'])->toBe('@laracms')
        ->and($seo['xCard'])->toBe('summary_large_image')
        ->and($seo['sitemapEnabled'])->toBeTrue()
        ->and($seo['sitemapChangeFrequency'])->toBe('Weekly')
        ->and($seo['sitemapPriority'])->toBe('0.7')
        ->and($seo['sitemapLimit'])->toBe('2000')
        ->and($seo['searchEnginesEnabled'])->toBeTrue()
        ->and($seo['searchEnginesIndexing'])->toBeTrue()
        ->and($seo['extraMetaTags'])->toBe('<meta name="google-site-verification" content="abc" />');
});

it('coerces absent booleans to false', function () {
    put(route('admin.seo.update'), [
        'metaDescription' => '',
        'siteName' => '',
        'namePosition' => 'After',
        'separator' => '|',
        'xCard' => 'summary',
        'sitemapChangeFrequency' => 'Monthly',
    ])->assertRedirect();

    $seo = Setting::firstOrCreate(['id' => 1])->seo;

    expect($seo['indexing'])->toBeFalse()
        ->and($seo['linkFollowing'])->toBeFalse()
        ->and($seo['noArchive'])->toBeFalse()
        ->and($seo['noImageIndex'])->toBeFalse()
        ->and($seo['noSnippet'])->toBeFalse()
        ->and($seo['sitemapEnabled'])->toBeFalse()
        ->and($seo['searchEnginesEnabled'])->toBeFalse()
        ->and($seo['searchEnginesIndexing'])->toBeFalse();
});

it('rejects invalid enum values', function () {
    put(route('admin.seo.update'), [
        'namePosition' => 'Sideways',
        'xCard' => 'bogus',
        'sitemapChangeFrequency' => 'Sometimes',
    ])->assertSessionHasErrors(['namePosition', 'xCard', 'sitemapChangeFrequency']);
});

it('loads defaults when no settings row exists', function () {
    Setting::query()->delete();

    get(route('admin.seo'))
        ->assertSuccessful()
        ->assertSee('en_US')
        ->assertSee('summary_large_image')
        ->assertSee('Monthly');
});

it('exposes the default SEO contract on the controller', function () {
    expect(SeoController::DEFAULT_SEO)
        ->toHaveKey('metaDescription')
        ->and(SeoController::DEFAULT_SEO['namePosition'])->toBe('After')
        ->and(SeoController::DEFAULT_SEO['separator'])->toBe('|')
        ->and(SeoController::DEFAULT_SEO['indexing'])->toBeTrue()
        ->and(SeoController::DEFAULT_SEO['linkFollowing'])->toBeTrue()
        ->and(SeoController::DEFAULT_SEO['noArchive'])->toBeFalse()
        ->and(SeoController::DEFAULT_SEO['noImageIndex'])->toBeFalse()
        ->and(SeoController::DEFAULT_SEO['noSnippet'])->toBeFalse()
        ->and(SeoController::DEFAULT_SEO['ogLocale'])->toBe('en_US')
        ->and(SeoController::DEFAULT_SEO['xHandle'])->toBe('@')
        ->and(SeoController::DEFAULT_SEO['xCard'])->toBe('summary_large_image')
        ->and(SeoController::DEFAULT_SEO['sitemapEnabled'])->toBeTrue()
        ->and(SeoController::DEFAULT_SEO['sitemapChangeFrequency'])->toBe('Monthly')
        ->and(SeoController::DEFAULT_SEO['sitemapPriority'])->toBe('0.5')
        ->and(SeoController::DEFAULT_SEO['sitemapLimit'])->toBe('1000')
        ->and(SeoController::DEFAULT_SEO['searchEnginesEnabled'])->toBeTrue()
        ->and(SeoController::DEFAULT_SEO['searchEnginesIndexing'])->toBeTrue()
        ->and(SeoController::DEFAULT_SEO['extraMetaTags'])->toBe('');
});
