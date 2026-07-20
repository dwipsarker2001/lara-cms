<?php

use App\Blocks\TemplateBlock;
use App\Models\DynamicBlock;

it('renders list loops and conditionals from dynamic block templates', function () {
    $block = new TemplateBlock(new DynamicBlock([
        'name' => 'testBlock',
        'label' => 'Test Block',
        'global' => false,
        'background' => true,
        'fields' => [
            [
                'name' => 'headline',
                'label' => 'Headline',
                'type' => 'string',
                'defaultValue' => 'Hello',
            ],
            [
                'name' => 'cards',
                'label' => 'Cards',
                'type' => 'object',
                'list' => true,
                'defaultCount' => 2,
                'fields' => [
                    [
                        'name' => 'title',
                        'label' => 'Title',
                        'type' => 'string',
                        'defaultValue' => 'Card',
                    ],
                    [
                        'name' => 'description',
                        'label' => 'Description',
                        'type' => 'string',
                        'defaultValue' => 'Desc',
                    ],
                ],
            ],
        ],
        'template' => '<div>{% for card in cards %}<div data-list="cards">{{ card.title }}</div>{% endfor %}{% if headline == "Hello" %}<span>ok</span>{% endif %}</div>',
    ]));

    $html = $block->render([
        'headline' => 'Hello',
        'cards' => [
            ['title' => 'Alpha', 'description' => 'One'],
            ['title' => 'Beta', 'description' => 'Two'],
        ],
    ]);

    expect($html)->toContain('<div data-list="cards">Alpha</div>')
        ->and($html)->toContain('<div data-list="cards">Beta</div>')
        ->and($html)->toContain('<span>ok</span>');
});
