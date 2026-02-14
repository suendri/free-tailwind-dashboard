<!DOCTYPE html>
<html lang="id" x-data="{ dark: localStorage.theme === 'dark' }" :class="{ 'dark': dark }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
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

<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100" x-data="{ open: true }" x-cloak>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out"
            :class="{
                '-translate-x-full lg:translate-x-0': !open,
                'translate-x-0': open,
                'lg:!w-16': !open
            }">
            <div class="h-full flex flex-col">
                <div
                    class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-800 lg:justify-start">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-8 w-8 bg-blue-600 rounded flex items-center justify-center text-white text-sm font-bold shrink-0">
                            L
                        </div>
                        <span x-show="open" x-transition:enter="transition-opacity duration-300 ease-in-out"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-300 ease-in-out"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="ml-3 font-semibold whitespace-nowrap">Laravel</span>
                    </div>
                    <button @click="open = false"
                        class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav x-data="{ activeMenu: null }"
                    class="flex-1 p-2 space-y-1 overflow-y-auto overflow-x-hidden whitespace-nowrap">

                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 rounded-md transition-colors group">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="transition-opacity duration-300 ease-in-out"
                            :class="!open ? 'opacity-0 w-0 overflow-hidden delay-0' : 'opacity-100 delay-0'">
                            Dashboard
                        </span>
                    </a>

                    <div>
                        <button @click="activeMenu = (activeMenu === 'produk' ? null : 'produk')"
                            class="w-full flex items-center justify-between px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="transition-opacity duration-300 ease-in-out"
                                    :class="!open ? 'opacity-0 w-0 overflow-hidden delay-0' : 'opacity-100 delay-0'">
                                    Produk
                                </span>
                            </div>
                            <svg :class="[activeMenu === 'produk' && 'rotate-180', !open ? 'opacity-0 w-0' : 'opacity-100']"
                                class="w-4 h-4 shrink-0 transition-all duration-300 ease-in-out" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="activeMenu === 'produk'" x-collapse.duration.300ms style="display: none;"
                            :class="!open && '!block'">
                            <div class="grid transition-[grid-template-rows,opacity] duration-300 ease-in-out"
                                :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                <div class="overflow-hidden">
                                    <a href="#"
                                        class="block px-3 py-2 ml-11 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md whitespace-nowrap transition-transform duration-300 ease-in-out"
                                        :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                        Daftar Produk
                                    </a>
                                    <a href="#"
                                        class="block px-3 py-2 ml-11 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md whitespace-nowrap transition-transform duration-300 ease-in-out"
                                        :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                        Tambah Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button @click="activeMenu = (activeMenu === 'kategori' ? null : 'kategori')"
                            class="w-full flex items-center justify-between px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="transition-opacity duration-300 ease-in-out"
                                    :class="!open ? 'opacity-0 w-0 overflow-hidden delay-0' : 'opacity-100 delay-0'">
                                    Kategori
                                </span>
                            </div>
                            <svg :class="[activeMenu === 'kategori' && 'rotate-180', !open ? 'opacity-0 w-0' : 'opacity-100']"
                                class="w-4 h-4 shrink-0 transition-all duration-300 ease-in-out" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="activeMenu === 'kategori'" x-collapse.duration.300ms style="display: none;"
                            :class="!open && '!block'">
                            <div class="grid transition-[grid-template-rows,opacity] duration-300 ease-in-out"
                                :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                                <div class="overflow-hidden">
                                    <a href="#"
                                        class="block px-3 py-2 ml-11 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md whitespace-nowrap transition-transform duration-300 ease-in-out"
                                        :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                        Daftar Kategori
                                    </a>
                                    <a href="#"
                                        class="block px-3 py-2 ml-11 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md whitespace-nowrap transition-transform duration-300 ease-in-out"
                                        :class="!open ? '-translate-x-5' : 'translate-x-0'">
                                        Tambah Kategori
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors group">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="transition-opacity duration-300 ease-in-out"
                            :class="!open ? 'opacity-0 w-0 overflow-hidden delay-0' : 'opacity-100 delay-0'">
                            Users
                        </span>
                    </a>

                    <a href="#"
                        class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors group">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="transition-opacity duration-300 ease-in-out"
                            :class="!open ? 'opacity-0 w-0 overflow-hidden delay-0' : 'opacity-100 delay-0'">
                            Settings
                        </span>
                    </a>
                </nav>

                <div class="p-2 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div
                            class="h-8 w-8 bg-blue-100 dark:bg-blue-950 rounded flex items-center justify-center text-blue-600 dark:text-blue-400 text-sm font-semibold shrink-0">
                            AD
                        </div>
                        <div x-show="open" x-transition:enter="transition-opacity duration-300 ease-in-out"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-300 ease-in-out"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="flex-1 min-w-0 overflow-hidden whitespace-nowrap">
                            <div class="text-sm font-medium truncate">Admin</div>
                            <div class="text-xs text-gray-500 truncate">admin@app.com</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header
                class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4">
                <div class="flex items-center gap-4">
                    <button @click="open = !open"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors  cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="font-semibold">Dashboard</h1>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        @click="dark = !dark; localStorage.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark')"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition-colors">
                        <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>

                    <div x-data="{ notif: false }" class="relative">
                        <button @click="notif = !notif"
                            class="relative p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border border-gray-200 dark:border-gray-800">
                    <h3 class="font-semibold text-lg mb-2">Konten Utama</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Coba tekan tombol hamburger di kiri atas. Menu akan bergeser dan konten ini akan melebar mengisi
                        layar.
                    </p>
                </div>
            </main>
        </div>
        <!-- Overlay untuk mobile -->
        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            x-transition.opacity></div>
    </div>
</body>

</html>
