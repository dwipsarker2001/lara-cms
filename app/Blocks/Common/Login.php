<?php

namespace App\Blocks\Common;

use App\Blocks\Block;
use App\Blocks\Field;

class Login extends Block
{
    public string $name = 'login';

    public string $label = 'Login';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::image('logo', 'Logo Image', default: '/placeholder-image.png'),
            Field::string('brandName', 'Brand Name', default: 'Lara CMS'),
            Field::string('headline', 'Headline', default: 'Bring your product stories to life in minutes'),
            Field::string('subtitle', 'Subtitle', default: ''),
            Field::boolean('googleEnabled', 'Enable Google Login', default: true),
            Field::string('googleLabel', 'Google Button Label', default: 'Continue with Google'),
            Field::boolean('microsoftEnabled', 'Enable Microsoft Login', default: true),
            Field::string('microsoftLabel', 'Microsoft Button Label', default: 'Continue with Microsoft'),
            Field::boolean('appleEnabled', 'Enable Apple Login', default: true),
            Field::string('appleLabel', 'Apple Button Label', default: 'Continue with Apple'),
            Field::string('dividerText', 'Divider Text', default: 'OR'),
            Field::string('emailPlaceholder', 'Email Placeholder', default: 'Enter your email...'),
            Field::string('passwordPlaceholder', 'Password Placeholder', default: 'Password'),
            Field::string('rememberLabel', 'Remember Me Label', default: 'Remember me'),
            Field::string('continueLabel', 'Continue Button Label', default: 'Continue'),
            Field::string('rightPanelHeading', 'Right Panel Heading', default: 'Join thousands of companies using Arcade — try it for free.'),
            Field::list('rightFeatures', 'Feature', [
                Field::string('text', 'Feature Text'),
            ], count: 3),
            Field::string('termsText', 'Terms Text', default: 'By continuing, you are indicating that you accept our'),
            Field::string('termsServiceLabel', 'Terms of Service Label', default: 'Terms of Service'),
            Field::link('termsServiceUrl', 'Terms of Service URL', default: '#'),
            Field::string('privacyLabel', 'Privacy Policy Label', default: 'Privacy Policy'),
            Field::link('privacyUrl', 'Privacy Policy URL', default: '#'),
            Field::boolean('showRegisterLink', 'Show Register Link', default: true),
            Field::string('registerLabel', 'Register Label', default: "Don't have an account?"),
            Field::string('registerLinkText', 'Register Link Text', default: 'Create one'),
            Field::link('registerUrl', 'Register URL', default: '/register'),
            Field::list('brandLogos', 'Brand Logos', [
                Field::image('image', 'Logo Image', default: '/placeholder-image.png'),
            ], count: 4),
        ];
    }
}
