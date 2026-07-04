@props([
    'action',
    'title' => 'Confirm action',
    'message' => 'Are you sure you want to continue?',
    'confirmLabel' => 'Yes, continue',
    'cancelLabel' => 'Cancel',
])

<span {{ $attributes->class('inline-flex') }} x-data="{ open: false }">
    <span x-on:click="open = true">
        {{ $trigger }}
    </span>

    <template x-teleport="body">
        <div
            class="fixed inset-0 z-[80] flex items-end justify-center px-4 py-6 sm:items-center"
            x-cloak
            x-show="open"
        >
            <div class="absolute inset-0 bg-gray-950/50" aria-hidden="true" x-transition.opacity></div>

            <section
                class="relative w-full max-w-md rounded-lg border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900"
                role="dialog"
                aria-modal="true"
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="translate-y-3 opacity-0 sm:scale-95 sm:translate-y-0"
                x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave-end="translate-y-3 opacity-0 sm:scale-95 sm:translate-y-0"
            >
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-950 dark:text-white">{{ $title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ $message }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-800 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        x-on:click="open = false"
                    >
                        {{ $cancelLabel }}
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-600/20 disabled:opacity-70"
                        x-on:click="$wire.{{ $action }}.then(() => open = false)"
                        wire:loading.attr="disabled"
                    >
                        {{ $confirmLabel }}
                    </button>
                </div>
            </section>
        </div>
    </template>
</span>
