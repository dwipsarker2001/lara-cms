<?php

namespace Database\Seeders;

use App\Blocks\BlockRegistry;
use App\Models\Page;
use App\Support\Sections;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $registry = app(BlockRegistry::class);

        $homeSections = array_filter([
            $registry->get('siteTopBar') ? Sections::createDefaultSection('siteTopBar') : null,
            $registry->get('siteNavbar') ? Sections::createDefaultSection('siteNavbar') : null,
            $registry->get('heroBanner') ? Sections::createDefaultSection('heroBanner') : null,
            $registry->get('aboutIntro') ? Sections::createDefaultSection('aboutIntro') : null,
            $registry->get('featureImageCards') ? Sections::createDefaultSection('featureImageCards') : null,
            $registry->get('travelDeals') ? Sections::createDefaultSection('travelDeals') : null,
            $registry->get('whyChooseUs') ? Sections::createDefaultSection('whyChooseUs') : null,
            $registry->get('teamCards') ? Sections::createDefaultSection('teamCards') : null,
            $registry->get('clientTestimonials') ? Sections::createDefaultSection('clientTestimonials') : null,
            $registry->get('latestBlog') ? Sections::createDefaultSection('latestBlog') : null,
            $registry->get('contact') ? Sections::createDefaultSection('contact') : null,
            $registry->get('siteFooter') ? Sections::createDefaultSection('siteFooter') : null,
        ]);

        Page::create([
            'slug' => 'home',
            'title' => 'Home',
            'sections' => $homeSections,
            'meta' => null,
            'published' => true,
            'position' => 0,
        ]);

        $this->command->info('Home page seeded with '.count($homeSections).' sections.');
    }
}
