<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class Faq extends Block
{
    public string $name = 'faq';

    public string $label = 'FAQ';

    public function fields(): array
    {
        return [
            Field::string('headline', 'Headline', default: 'Frequently Asked Questions'),
            Field::text('subtitle', 'Subtitle', default: 'Everything you need to know about the product and billing. Can\'t find what you\'re looking for? Reach out to our support team.'),
            Field::list('faqs', 'FAQ Item', [
                Field::string('question', 'Question', default: 'What is the product?'),
                Field::richText('answer', 'Answer', default: 'Our product is a comprehensive task management platform designed to help teams organize, track, and collaborate on projects efficiently from a single dashboard.'),
            ], count: 5),
        ];
    }
}
