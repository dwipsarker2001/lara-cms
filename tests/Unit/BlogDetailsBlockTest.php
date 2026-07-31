<?php

use App\Blocks\BlockRegistry;
use App\Blocks\custom\BlogDetails;

it('registers and resolves the blogDetails block', function () {
    $registry = app(BlockRegistry::class);
    $block = $registry->get('blogDetails');

    expect($block)->toBeInstanceOf(BlogDetails::class);
    expect($block->name)->toBe('blogDetails');
    expect($block->label)->toBe('Blog Details');
});
