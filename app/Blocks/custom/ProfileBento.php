<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;

class ProfileBento extends Block
{
    public string $name = 'profileBento';

    public string $label = 'Profile Bento';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('profileImage', 'Profile Image'),
            Field::string('profileName', 'Profile Name'),
            Field::text('profileRole', 'Profile Role'),
            Field::string('profileStatus', 'Profile Status', default: 'Available'),
            Field::list('profileSocial', 'Profile Social', [
                Field::icon('icon', 'Icon'),
                Field::string('platform', 'Platform'),
                Field::link('url', 'URL'),
            ], count: 4),
            Field::text('aboutText', 'About Text'),
            Field::text('quoteText', 'Quote Text'),
            Field::image('imageTopRight', 'Image Top Right'),
            Field::image('imageBottomLeft', 'Image Bottom Left'),
            Field::list('stats', 'Stats', [
                Field::icon('icon', 'Icon'),
                Field::string('count', 'Count', default: '1000'),
                Field::string('handle', 'Handle', default: '@travelagency'),
            ], count: 4),
        ];
    }
}
