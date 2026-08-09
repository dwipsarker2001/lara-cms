<?php

use App\Blocks\BlockRegistry;
use App\Blocks\custom\PackageList;
use App\Models\Taxonomy;
use App\Models\Term;

test('BlockRegistry discovers PackageList block subclass', function () {
    $registry = app(BlockRegistry::class);
    $all = $registry->all();

    expect($all)->toHaveKey('packageList')
        ->and($all['packageList'])->toBeInstanceOf(PackageList::class);
});

test('PackageList block exposes destinationTaxonomy and categoryTaxonomy fields', function () {
    $block = new PackageList;
    $fieldNames = array_column($block->resolvedFields(), 'name');

    expect($fieldNames)->toContain('destinationTaxonomy');
    expect($fieldNames)->toContain('categoryTaxonomy');
});

test('PackageList block renders view successfully with empty and filled taxonomy settings', function () {
    $destTax = Taxonomy::create(['title' => 'Destinations', 'slug' => 'destinations']);
    Term::create(['taxonomy_id' => $destTax->id, 'title' => 'Sylhet', 'slug' => 'sylhet']);

    $catTax = Taxonomy::create(['title' => 'Categories', 'slug' => 'categories']);
    Term::create(['taxonomy_id' => $catTax->id, 'title' => 'Adventure', 'slug' => 'adventure']);

    $block = new PackageList;

    $html = $block->render(data: [
        'packageCollection' => '',
        'destinationTaxonomy' => 'destinations',
        'categoryTaxonomy' => 'categories',
        'packagesPerPage' => 6,
        'priceMax' => 500000,
    ]);

    expect($html)->toBeString()
        ->toContain('Sylhet')
        ->toContain('Adventure');
});
