<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: document.documentElement.classList.contains('dark'), open: window.innerWidth >= 1024 }" :class="{ 'dark': dark }">
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

    <body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100" x-cloak>
        <div class="flex h-screen">
            <aside
                class="fixed inset-y-0 left-0 z-50 w-64 border-r border-gray-200 bg-white transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900 lg:static"
                :class="{
                    '-translate-x-full lg:translate-x-0': !open,
                    'translate-x-0': open,
                    'lg:!w-16': !open
                }"
            >
                <div class="flex h-full flex-col">
                    <div class="flex h-16 items-center justify-between border-b border-gray-200 px-4 dark:border-gray-800 lg:justify-start">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-blue-600 text-sm font-bold text-white">
                                L
                            </div>
                            <span
                                x-show="open"
                                x-transition:enter="transition-opacity duration-300 ease-in-out"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-300 ease-in-out"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="ml-3 whitespace-nowrap font-semibold"
                            >
                                {{ config('app.name', 'Laravel') }}
                            </span>
                        </div>
                        <button
                            type="button"
                            @click="open = false"
                            class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 lg:hidden"
                            aria-label="Tutup sidebar"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <nav x-data="{ activeMenu: null }" class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden whitespace-nowrap p-2">
                        <a
                            href="{{ route('dashboard') }}"
                            class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors group {{ request()->routeIs('dashboard', 'home') ? 'bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800' }}"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" />
                            </svg>
                            <span class="transition-opacity duration-300 ease-in-out" :class="!open ? 'w-0 overflow-hidden opacity-0 delay-0' : 'opacity-100 delay-0'">
                                Dashboard
                            </span>
                        </a>

                        <div>
                            <button
                                type="button"
                                @click="activeMenu = (activeMenu === 'produk' ? null : 'produk')"
                                class="flex w-full items-center justify-between rounded-md px-3 py-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800 group"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9Z" />
                                    </svg>
                                    <span class="transition-opacity duration-300 ease-in-out" :class="!open ? 'w-0 overflow-hidden opacity-0 delay-0' : 'opacity-100 delay-0'">
                                        Produk
                                    </span>
                                </div>
                                <svg
                                    :class="[activeMenu === 'produk' && 'rotate-180', !open ? 'w-0 opacity-0' : 'opacity-100']"
                                    class="h-4 w-4 shrink-0 transition-all duration-300 ease-in-out"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="activeMenu === 'produk'" x-collapse.duration.300ms style="display: none;" :class="!open && '!block'">
                                <div class="grid transition-[grid-template-rows,opacity] duration-300 ease-in-out" :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                    <div class="overflow-hidden">
                                        <a href="#" class="ml-11 block rounded-md px-3 py-2 text-gray-600 transition-transform duration-300 ease-in-out hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800" :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                            Daftar Produk
                                        </a>
                                        <a href="#" class="ml-11 block rounded-md px-3 py-2 text-gray-600 transition-transform duration-300 ease-in-out hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800" :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                            Tambah Baru
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button
                                type="button"
                                @click="activeMenu = (activeMenu === 'kategori' ? null : 'kategori')"
                                class="flex w-full items-center justify-between rounded-md px-3 py-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800 group"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 3 12V7a4 4 0 0 1 4-4Z" />
                                    </svg>
                                    <span class="transition-opacity duration-300 ease-in-out" :class="!open ? 'w-0 overflow-hidden opacity-0 delay-0' : 'opacity-100 delay-0'">
                                        Kategori
                                    </span>
                                </div>
                                <svg
                                    :class="[activeMenu === 'kategori' && 'rotate-180', !open ? 'w-0 opacity-0' : 'opacity-100']"
                                    class="h-4 w-4 shrink-0 transition-all duration-300 ease-in-out"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="activeMenu === 'kategori'" x-collapse.duration.300ms style="display: none;" :class="!open && '!block'">
                                <div class="grid transition-[grid-template-rows,opacity] duration-300 ease-in-out" :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                    <div class="overflow-hidden">
                                        <a href="#" class="ml-11 block rounded-md px-3 py-2 text-gray-600 transition-transform duration-300 ease-in-out hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800" :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                            Daftar Kategori
                                        </a>
                                        <a href="#" class="ml-11 block rounded-md px-3 py-2 text-gray-600 transition-transform duration-300 ease-in-out hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800" :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                            Tambah Kategori
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800 group">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                            </svg>
                            <span class="transition-opacity duration-300 ease-in-out" :class="!open ? 'w-0 overflow-hidden opacity-0 delay-0' : 'opacity-100 delay-0'">
                                Users
                            </span>
                        </a>

                        <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-gray-800 group">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span class="transition-opacity duration-300 ease-in-out" :class="!open ? 'w-0 overflow-hidden opacity-0 delay-0' : 'opacity-100 delay-0'">
                                Settings
                            </span>
                        </a>
                    </nav>

                    <div class="border-t border-gray-200 p-2 dark:border-gray-800">
                        <div class="flex items-center gap-3 px-3 py-2">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-blue-100 text-sm font-semibold text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                                {{ auth()->check() ? mb_substr(auth()->user()->name, 0, 2) : 'AD' }}
                            </div>
                            <div
                                x-show="open"
                                x-transition:enter="transition-opacity duration-300 ease-in-out"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-300 ease-in-out"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="min-w-0 flex-1 overflow-hidden whitespace-nowrap"
                            >
                                <div class="truncate text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</div>
                                <div class="truncate text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@app.com' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="open = !open"
                            class="cursor-pointer text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                            aria-label="Toggle sidebar"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="font-semibold">{{ $pageTitle ?? $title ?? 'Dashboard' }}</h1>
                            @isset($pageSubtitle)
                                <p class="hidden text-xs text-gray-500 dark:text-gray-400 sm:block">{{ $pageSubtitle }}</p>
                            @endisset
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="dark = !dark; localStorage.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark)"
                            class="rounded p-2 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
                            aria-label="Toggle dark mode"
                        >
                            <svg x-show="dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m12.728 0-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                            </svg>
                            <svg x-show="!dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                            </svg>
                        </button>

                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100">
                                    Logout
                                </button>
                            </form>
                        @endauth
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </main>
            </div>

            <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-transition.opacity></div>
        </div>

        @livewireScripts
    </body>
</html>
