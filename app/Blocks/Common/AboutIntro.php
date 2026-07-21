<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class AboutIntro extends Block
{
    public string $name = 'aboutIntro';

    public string $label = 'About Intro';

    public function fields(): array
    {
        return [
            Field::image('image1', 'Image 1 (top left)', default: '/placeholder-image.png'),
            Field::image('image2', 'Image 2 (bottom left)', default: '/placeholder-image.png'),
            Field::image('image3', 'Image 3 (bottom right)', default: '/placeholder-image.png'),
            Field::image('badge', 'Badge Image', default: '/placeholder-image.png'),
            Field::string('heading', 'Heading', default: "Why We're Best Agency"),
            Field::string('subheading', 'Subheading', default: 'Our expertise is your peace of mind.'),
            Field::text('body1', 'Paragraph 1', default: 'A short paragraph describing what makes your agency stand out.'),
            Field::text('body2', 'Paragraph 2', default: 'A second paragraph with supporting detail.'),
            Field::image('signature', 'Signature Image', default: '/placeholder-image.png'),
            Field::string('signerName', 'Signer Name', default: 'Jane Doe'),
            Field::string('signerTitle', 'Signer Title', default: 'Founder & CEO'),
        ];
    }
}
