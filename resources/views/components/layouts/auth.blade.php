<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: document.documentElement.classList.contains('dark') }" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <title>{{ $title ?? config('app.name', 'Dashboard') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>

    <body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100" x-cloak>
        <main class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full max-w-6xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:min-h-[680px] lg:grid-cols-[minmax(0,1fr)_minmax(420px,500px)]">
                <section class="hidden bg-sky-50 p-10 text-slate-950 dark:bg-slate-900 lg:flex lg:flex-col lg:justify-between">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded bg-blue-600 text-sm font-bold text-white">L</span>
                        <span class="text-lg font-semibold">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    <div class="max-w-xl">
                        {{ $aside ?? '' }}
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div class="rounded-lg border border-sky-200 bg-white/70 p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <p class="text-xs text-slate-500 dark:text-gray-400">Auth</p>
                            <p class="mt-1 font-semibold">Fortify</p>
                        </div>
                        <div class="rounded-lg border border-sky-200 bg-white/70 p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <p class="text-xs text-slate-500 dark:text-gray-400">UI</p>
                            <p class="mt-1 font-semibold">Tailwind</p>
                        </div>
                        <div class="rounded-lg border border-sky-200 bg-white/70 p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <p class="text-xs text-slate-500 dark:text-gray-400">App</p>
                            <p class="mt-1 font-semibold">Livewire</p>
                        </div>
                    </div>
                </section>

                <section class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-5 py-10 sm:px-10 lg:min-h-full">
                    <div class="w-full max-w-md">
                        <div class="mb-8 flex items-center justify-between">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 lg:hidden">
                                <span class="flex h-9 w-9 items-center justify-center rounded bg-blue-600 text-sm font-bold text-white">L</span>
                                <span class="font-semibold">{{ config('app.name', 'Laravel') }}</span>
                            </a>

                            <button
                                type="button"
                                @click="dark = !dark; localStorage.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark)"
                                class="ml-auto rounded-md border border-gray-200 p-2 text-gray-600 transition-colors hover:bg-gray-100 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-950"
                                aria-label="Toggle dark mode"
                            >
                                <svg x-show="dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m12.728 0-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                                </svg>
                                <svg x-show="!dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                                </svg>
                            </button>
                        </div>

                        {{ $slot }}
                    </div>
                </section>
            </div>
        </main>

        @livewireScriptConfig
    </body>
</html>
