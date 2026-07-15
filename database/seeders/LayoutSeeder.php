<?php

namespace Database\Seeders;

use App\Models\Layout;
use Illuminate\Database\Seeder;

class LayoutSeeder extends Seeder
{
    public function run(): void
    {
        Layout::create([
            'name' => 'Default Page',
            'collection' => 'page',
            'sections' => [],
            'position' => 1,
        ]);

        Layout::create([
            'name' => 'Default Blog',
            'collection' => 'blog',
            'sections' => [],
            'position' => 2,
        ]);
    }
}
