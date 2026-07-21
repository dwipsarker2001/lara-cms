<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Register</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-root antialiased bg-white min-h-full">
    @include('blocks.register', ['data' => array_merge([
        'brandName' => config('app.name'),
        'headline' => 'Create your account',
        'subtitle' => 'Get started with email marketing',
        'namePlaceholder' => 'John Doe',
        'emailPlaceholder' => 'you@example.com',
        'passwordPlaceholder' => 'Minimum 8 characters',
        'passwordConfirmPlaceholder' => 'Repeat your password',
        'submitLabel' => 'Create account',
        'rightPanelHeading' => 'Start your journey — manage projects and teams with ease.',
        'rightFeatures' => [
            ['text' => 'No credit card required'],
            ['text' => 'Share in minutes — no code needed'],
            ['text' => 'Drive conversions with AI-powered tools'],
        ],
        'showLoginLink' => true,
        'loginLabel' => 'Already have an account?',
        'loginLinkText' => 'Sign in',
        'loginUrl' => route('login'),
    ], $data ?? []), 'preview' => false])
</body>
</html>
