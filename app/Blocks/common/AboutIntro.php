<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class AboutIntro extends Block
{
    public string $name = 'aboutIntro';

    public string $label = 'About Intro';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('image1', 'Image 1'),
            Field::image('image2', 'Image 2'),
            Field::image('image3', 'Image 3'),
            Field::image('badge', 'Badge'),
            Field::string('heading', 'Heading', default: "Why We're Best Agency"),
            Field::string('subheading', 'Subheading'),
            Field::text('body1', 'Body 1'),
            Field::text('body2', 'Body 2'),
            Field::image('signature', 'Signature'),
            Field::string('signerName', 'Signer Name', default: 'Jane Doe'),
            Field::string('signerTitle', 'Signer Title', default: 'Founder & CEO'),
        ];
    }
}
