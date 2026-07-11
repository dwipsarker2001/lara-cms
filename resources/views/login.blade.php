<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-root antialiased bg-header-bg text-text-primary min-h-full flex items-center justify-center">
    <div class="w-full max-w-sm mx-4">
        <div class="bg-content-bg rounded-2xl border border-content-border shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-text-heading">{{ config('app.name', 'Laravel') }}</h1>
                <p class="text-sm text-text-muted mt-1">Sign in to your account</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-lg bg-danger/10 text-danger text-sm">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-text-heading mb-1.5">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full h-10 px-3 rounded-lg border border-content-border bg-body-bg text-sm text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                        placeholder="you@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-text-heading mb-1.5">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full h-10 px-3 rounded-lg border border-content-border bg-body-bg text-sm text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-colors"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-text-muted cursor-pointer">
                        <input type="checkbox" name="remember" class="size-4 rounded border-gray-300 accent-text-heading">
                        Remember me
                    </label>
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                >
                    Sign in
                </button>
            </form>
        </div>
        <p class="text-center text-xs text-text-muted/60 mt-6">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
