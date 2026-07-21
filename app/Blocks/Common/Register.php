<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class Register extends Block
{
    public string $name = 'register';

    public string $label = 'Register';

    public bool $background = false;

    public function view(): string
    {
        return 'blocks.auth.register';
    }

    public function fields(): array
    {
        return [
            Field::image('logo', 'Logo Image', default: '/placeholder-image.png'),
            Field::string('brandName', 'Brand Name', default: 'Lara CMS'),
            Field::string('headline', 'Headline', default: 'Create your account'),
            Field::string('subtitle', 'Subtitle', default: 'Get started with email marketing'),
            Field::string('googleLabel', 'Google Button Label', default: 'Google'),
            Field::string('microsoftLabel', 'Microsoft Button Label', default: 'Microsoft'),
            Field::string('appleLabel', 'Apple Button Label', default: 'Apple'),
            Field::string('dividerText', 'Divider Text', default: 'OR'),
            Field::string('namePlaceholder', 'Name Placeholder', default: 'John Doe'),
            Field::string('emailPlaceholder', 'Email Placeholder', default: 'you@example.com'),
            Field::string('passwordPlaceholder', 'Password Placeholder', default: 'Minimum 8 characters'),
            Field::string('passwordConfirmPlaceholder', 'Confirm Password Placeholder', default: 'Repeat your password'),
            Field::string('submitLabel', 'Submit Button Label', default: 'Create account'),
            Field::string('rightPanelHeading', 'Right Panel Heading', default: 'Join thousands of companies using Arcade — try it for free.'),
            Field::list('rightFeatures', 'Feature', [
                Field::string('text', 'Feature Text', default: 'No credit card required'),
            ], count: 3),
            Field::boolean('showLoginLink', 'Show Login Link', default: true),
            Field::string('loginLabel', 'Login Label', default: 'Already have an account?'),
            Field::string('loginLinkText', 'Login Link Text', default: 'Sign in'),
            Field::link('loginUrl', 'Login URL', default: '/login'),
            Field::list('brandLogos', 'Brand Logos', [
                Field::image('image', 'Logo Image', default: '/placeholder-image.png'),
            ], count: 4),
        ];
    }
}
