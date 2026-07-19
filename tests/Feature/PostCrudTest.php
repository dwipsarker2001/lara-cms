<?php

// Blog (Post) feature has been deactivated. These tests are skipped.

it('is skipped because Blog feature is deactivated', function () {
    expect(true)->toBe(true);
})->skip();
