<?php

use App\Support\CommandSearch;

it('scores empty query as a default match', function () {
    expect(CommandSearch::score(['title' => 'Dashboard'], ''))->toBe(1.0);
});

it('ranks prefix matches highest', function () {
    $cmd = ['title' => 'Blog Post', 'subtitle' => null, 'keywords' => null];

    expect(CommandSearch::score($cmd, 'blog'))->toBe(4.0);
});

it('ranks word-boundary matches above substring', function () {
    $boundary = ['title' => 'Content » Blog', 'subtitle' => null, 'keywords' => null];
    $substring = ['title' => 'ablogz', 'subtitle' => null, 'keywords' => null];

    expect(CommandSearch::score($boundary, 'blog'))->toBe(3.0);
    expect(CommandSearch::score($substring, 'blog'))->toBe(1.0);
});

it('falls back to fuzzy subsequence scoring', function () {
    $cmd = ['title' => 'Blog Post', 'subtitle' => null, 'keywords' => null];

    expect(CommandSearch::score($cmd, 'blgpst'))->toBe(0.5);
});

it('returns zero when nothing matches', function () {
    $cmd = ['title' => 'Dashboard', 'subtitle' => null, 'keywords' => null];

    expect(CommandSearch::score($cmd, 'zzzz'))->toBe(0.0);
});

it('filters and ranks commands by score then input order', function () {
    $commands = [
        ['id' => '1', 'group' => 'A', 'title' => 'xblog', 'href' => '/a'],
        ['id' => '2', 'group' => 'A', 'title' => 'Blog', 'href' => '/b'],
        ['id' => '3', 'group' => 'A', 'title' => 'Dashboard', 'href' => '/c'],
        ['id' => '4', 'group' => 'A', 'title' => 'Content Blog', 'href' => '/d'],
    ];

    $ranked = CommandSearch::filter($commands, 'blog');

    expect(array_column($ranked, 'id'))->toBe(['2', '4', '1']);
});

it('groups commands preserving first-seen order', function () {
    $commands = [
        ['id' => '1', 'group' => 'Pages', 'title' => 'Home', 'href' => '/'],
        ['id' => '2', 'group' => 'Blog', 'title' => 'Post', 'href' => '/p'],
        ['id' => '3', 'group' => 'Pages', 'title' => 'About', 'href' => '/a'],
    ];

    $groups = CommandSearch::group($commands);

    expect(array_column($groups, 'group'))->toBe(['Pages', 'Blog']);
    expect(array_column($groups[0]['items'], 'id'))->toBe(['1', '3']);
});

it('matches keywords and subtitles', function () {
    $cmd = [
        'title' => 'SEO Pro',
        'subtitle' => 'settings',
        'keywords' => 'meta search engine',
    ];

    expect(CommandSearch::score($cmd, 'meta'))->toBeGreaterThan(0);
    expect(CommandSearch::score($cmd, 'settings'))->toBeGreaterThan(0);
});
