<?php

namespace App\Blocks\Home;

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
            Field::text('description', 'Description'),
            Field::list('members', 'Members', [
                Field::image('image', 'Image'),
                Field::string('name', 'Name', default: 'Team Member'),
                Field::string('role', 'Role', default: 'Role'),
                Field::list('social', 'Social', [
                    Field::icon('icon', 'Icon'),
                    Field::string('platform', 'Platform'),
                    Field::link('url', 'URL'),
                ], count: 3),
            ], count: 4),
        ];
    }
}
