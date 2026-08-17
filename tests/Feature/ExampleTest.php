<?php

use App\Models\Collection;

it('returns a successful response', function () {
    $collection = Collection::create(['name' => 'Pages', 'slug' => 'pages']);
    $collection->entries()->create([
        'slug' => 'home',
        'data' => ['title' => 'Home'],
        'published' => true,
        'sections' => [],
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
});
