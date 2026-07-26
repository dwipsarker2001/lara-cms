<?php

use App\Models\User;

it('previews travel details block in editor preview without exception', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'admin')->postJson('/admin/preview', [
        'sections' => [
            [
                '_key' => 'sec-1',
                'name' => 'travelDetails',
                'data' => [
                    'title' => 'Sample Package',
                    'bookNowLabel' => 'Book Now',
                ],
            ],
        ],
    ]);

    $response->assertStatus(200);
});
