@php $d = $data; @endphp

@unless($preview ?? false)
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
<body class="antialiased bg-white min-h-full">
@endunless

<div data-block="register" class="min-h-screen w-full flex bg-white">
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-16">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-center gap-2 mb-10">
                @if($d['logo'] ?? false)
                    <div data-edit="logo">
                        <img src="{{ $d['logo'] }}" alt="{{ $d['brandName'] ?? 'Lara CMS' }}" class="h-8 w-auto">
                    </div>
                @else
                    <span data-edit="brandName" class="text-xl font-extrabold tracking-tight text-gray-900">{{ $d['brandName'] ?? 'Lara CMS' }}</span>
                @endif
            </div>

            <h1 data-edit="headline" class="text-center text-[26px] leading-tight font-bold text-gray-900 mb-8">
                {{ $d['headline'] ?? 'Create your account' }}
            </h1>
            @if($d['subtitle'] ?? false)
                <p data-edit="subtitle" class="text-center text-sm text-gray-500 mb-8">{{ $d['subtitle'] }}</p>
            @endif

            @if($d['googleEnabled'] ?? true)
            <div class="flex gap-3">
                <a href="{{ url('/auth/google') }}"
                   class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition shadow-sm no-underline">
                    <svg width="16" height="16" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.6 20.5H42V20.4H24v7.2h11.3C33.7 32 29.3 35 24 35c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.1-5.1C33.9 6.2 29.2 4.3 24 4.3 12.9 4.3 4 13.2 4 24.4s8.9 20.1 20 20.1c11.5 0 19.2-8.1 19.2-19.5 0-1.3-.1-2.3-.3-3.5z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.9 18.9 13 24 13c3.1 0 5.9 1.2 8 3.1l5.1-5.1C33.9 6.9 29.2 5 24 5c-7.5 0-14 4.2-17.7 10.4z"/>
                        <path fill="#4CAF50" d="M24 44c5.1 0 9.8-1.9 13.3-5.1l-6.1-5.2c-2 1.4-4.6 2.3-7.2 2.3-5.3 0-9.7-3.6-11.3-8.4l-6.5 5C9.8 39.8 16.4 44 24 44z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20.4H24v7.2h11.3c-.8 2.3-2.3 4.2-4.2 5.6l6.1 5.2C40.6 35.9 44 30.6 44 24.4c0-1.3-.1-2.6-.4-3.9z"/>
                    </svg>
                    <span data-edit="googleLabel">{{ $d['googleLabel'] ?? 'Google' }}</span>
                </a>

                @if($d['microsoftEnabled'] ?? true)
                <button type="button" class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 23 23">
                        <rect x="1" y="1" width="10" height="10" fill="#F35325"/>
                        <rect x="12" y="1" width="10" height="10" fill="#81BC06"/>
                        <rect x="1" y="12" width="10" height="10" fill="#05A6F0"/>
                        <rect x="12" y="12" width="10" height="10" fill="#FFBA08"/>
                    </svg>
                    <span data-edit="microsoftLabel">{{ $d['microsoftLabel'] ?? 'Microsoft' }}</span>
                </button>
                @endif

                @if($d['appleEnabled'] ?? true)
                <button type="button" class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50 transition shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 384 512" fill="#111827">
                        <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5c0 26.2 4.8 53.3 14.4 81.2 12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/>
                    </svg>
                    <span data-edit="appleLabel">{{ $d['appleLabel'] ?? 'Apple' }}</span>
                </button>
                @endif
            </div>
            @endif

            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span data-edit="dividerText" class="text-xs text-gray-400 font-medium">{{ $d['dividerText'] ?? 'OR' }}</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            @if($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ $errors->first('email') ?? $errors->first('name') }}
                </div>
            @endif

            <form method="POST" action="{{ url('/register') }}" class="space-y-3">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ $d['namePlaceholder'] ?? 'John Doe' }}" required autofocus
                    class="w-full rounded-xl bg-gray-100 border border-gray-200 px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition @error('name') ring-2 ring-red-300 @enderror">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ $d['emailPlaceholder'] ?? 'you@example.com' }}" required autocomplete="username"
                    class="w-full rounded-xl bg-gray-100 border border-gray-200 px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition @error('email') ring-2 ring-red-300 @enderror">
                <input type="password" name="password" placeholder="{{ $d['passwordPlaceholder'] ?? 'Minimum 8 characters' }}" required autocomplete="new-password"
                    class="w-full rounded-xl bg-gray-100 border border-gray-200 px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition @error('password') ring-2 ring-red-300 @enderror">
                <input type="password" name="password_confirmation" placeholder="{{ $d['passwordConfirmPlaceholder'] ?? 'Repeat your password' }}" required autocomplete="new-password"
                    class="w-full rounded-xl bg-gray-100 border border-gray-200 px-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-gray-300 transition">
                <button type="submit" data-edit="submitLabel" data-edit-button class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800 transition shadow-sm">
                    {{ $d['submitLabel'] ?? 'Create account' }}
                </button>
            </form>

            @if(($d['showLoginLink'] ?? true) && ($d['loginLabel'] ?? false))
            <p class="text-center text-sm text-gray-400 mt-6">
                <span data-edit="loginLabel">{{ $d['loginLabel'] }}</span>
                <a href="{{ $d['loginUrl'] ?? route('login') }}" data-edit="loginLinkText" class="font-semibold text-gray-900 hover:underline">{{ $d['loginLinkText'] ?? 'Sign in' }}</a>
            </p>
            @endif
        </div>
    </div>

    <div class="hidden lg:flex w-1/2 relative overflow-hidden">
        <div class="absolute inset-4 rounded-3xl overflow-hidden" style="background: linear-gradient(160deg, #a9d3f2 0%, #cfe4f4 35%, #f3f7fb 70%, #ffffff 100%);">
            <svg class="absolute inset-0 w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse" patternTransform="rotate(15)">
                        <path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>

            <div class="relative h-full flex flex-col justify-end p-12 pb-16">
                <div class="mb-6">
                    <p data-edit="rightPanelHeading" class="text-gray-900 font-bold text-lg leading-snug">
                        {{ $d['rightPanelHeading'] ?? 'Join thousands of companies using Arcade — try it for free.' }}
                    </p>
                </div>

                @if(($d['rightFeatures'] ?? []) && count($d['rightFeatures'] ?? []) > 0)
                <ul class="space-y-2 mb-8">
                    @foreach($d['rightFeatures'] as $feature)
                        @if($feature)
                        <li data-list="rightFeatures" class="flex items-center gap-2 text-sm text-gray-800 font-medium">
                            <span class="text-blue-600">✓</span>
                            <span data-edit="text">{{ $feature['text'] ?? '' }}</span>
                        </li>
                        @endif
                    @endforeach
                </ul>
                @endif

                @if(($d['brandLogos'] ?? []) && count($d['brandLogos'] ?? []) > 0)
                <div class="flex items-center gap-5 opacity-60">
                    @foreach($d['brandLogos'] as $logo)
                        @if($logo && ($logo['image'] ?? false))
                        <div data-list="brandLogos" data-edit="image">
                            <img src="{{ $logo['image'] }}" alt="" class="h-6 w-auto">
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@unless($preview ?? false)
</body>
</html>
@endunless
