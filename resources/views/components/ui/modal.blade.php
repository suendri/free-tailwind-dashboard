@props([
    'name',
    'title' => null,
    'description' => null,
    'maxWidth' => 'md',
    'closeable' => true,
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === @js($name) || $event.detail?.name === @js($name)) open = true"
    x-on:close-modal.window="if (!$event.detail || Object.keys($event.detail).length === 0 || $event.detail === @js($name) || $event.detail?.name === @js($name)) open = false"
    x-show="open"
    x-transition.opacity
    class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 px-4 py-6"
    style="display: none;"
>
    <div
        {{ $attributes->class([
            'w-full rounded-lg border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900',
            $maxWidthClass,
        ]) }}
    >
        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                @if ($title)
                    <h3 class="text-lg font-semibold text-gray-950 dark:text-white">{{ $title }}</h3>
                @endif

                @if ($description)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>

            @if ($closeable)
                <button
                    type="button"
                    x-on:click="open = false"
                    class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    aria-label="Close modal"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        {{ $slot }}
    </div>
</div>
