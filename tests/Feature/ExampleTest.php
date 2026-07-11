<?php

use App\Models\Page;

it('returns a successful response', function () {
    Page::create([
        'title' => 'Home',
        'slug' => 'home',
        'published' => true,
        'sections' => [],
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
});
