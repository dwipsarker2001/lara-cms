<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Statamic') }} - Sign in</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter',sans-serif] bg-[#f4f4f6] text-gray-900 min-h-full flex items-center justify-center p-4 sm:p-6 antialiased selection:bg-[#5538ee] selection:text-white">
    <div class="w-full max-w-md sm:max-w-lg mx-auto">
        <!-- Main Card -->
        <div class="bg-white rounded-[20px] border border-gray-200/80 shadow-[0_12px_32px_-4px_rgba(0,0,0,0.06),0_2px_6px_-1px_rgba(0,0,0,0.04)] p-6 sm:p-8">
            <!-- Icon Badge -->
            <div class="flex justify-center mb-5">
                <div class="w-12 h-12 rounded-[14px] border border-gray-200/90 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.04)] flex items-center justify-center text-gray-800">
                    <svg class="w-5 h-5 stroke-[1.75]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                </div>
            </div>

            <!-- Header Title & Subtitle -->
            <div class="text-center mb-7">
                <h1 class="text-[21px] font-semibold text-gray-900 tracking-tight">Sign in with email</h1>
                <p class="text-[14px] text-gray-500 mt-1 font-normal">Sign into your Statamic Control Panel</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-[10px] bg-red-50 border border-red-200/80 text-red-600 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-medium">{{ $errors->first('email') ?: $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-[14px] font-medium text-gray-900 mb-1.5">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', 'admin@example.com') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="admin@example.com"
                        class="w-full h-11 px-3.5 rounded-[10px] border border-gray-200 bg-white text-[14px] text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5538ee]/20 focus:border-[#5538ee] shadow-[0_1px_2px_rgba(0,0,0,0.03)] transition-all duration-150"
                    >
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-[14px] font-medium text-gray-900">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[14px] font-medium text-[#5538ee] hover:underline hover:text-[#432bd3] transition-colors">Forgot password?</a>
                        @else
                            <a href="#" class="text-[14px] font-medium text-[#5538ee] hover:underline hover:text-[#432bd3] transition-colors">Forgot password?</a>
                        @endif
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••••••"
                        class="w-full h-11 px-3.5 rounded-[10px] border border-gray-200 bg-white text-[14px] text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5538ee]/20 focus:border-[#5538ee] shadow-[0_1px_2px_rgba(0,0,0,0.03)] transition-all duration-150"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-0.5">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-[18px] h-[18px] rounded-[5px] border-gray-300 text-[#5538ee] focus:ring-[#5538ee]/20 focus:ring-offset-0 transition-colors"
                        >
                        <span class="text-[14px] text-gray-700 font-normal">Remember me</span>
                    </label>
                </div>

                <!-- Continue Button -->
                <button
                    type="submit"
                    class="w-full h-11 px-4 rounded-[10px] bg-[#5538ee] hover:bg-[#432bd3] active:bg-[#3822b8] text-white text-[14px] font-medium transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#5538ee]/40 focus:ring-offset-1 flex items-center justify-center cursor-pointer"
                >
                    Continue
                </button>
            </form>
        </div>
    </div>
</body>
</html>
