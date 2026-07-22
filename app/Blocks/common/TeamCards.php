<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class TeamCards extends Block
{
    public string $name = 'teamCards';

    public string $label = 'Team Cards';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Meet our Team'),
            Field::text('description', 'Description', default: 'Meet the dedicated team behind our travels — passionate experts committed to making your journey unforgettable.'),
            Field::list('members', 'Members', [
                Field::image('image', 'Image', default: '/placeholder-image.png'),
                Field::string('name', 'Name', default: 'Team Member'),
                Field::string('role', 'Role', default: 'Role'),
                Field::list('social', 'Social', [
                    Field::icon('icon', 'Icon', default: 'fa-brands fa-linkedin'),
                    Field::string('platform', 'Platform', default: 'linkedin'),
                    Field::link('url', 'URL', default: '#'),
                ], count: 3),
            ], count: 4),
        ];
    }
}
