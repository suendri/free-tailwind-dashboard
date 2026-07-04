@if ($paginator->hasPages())
    <nav class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between" role="navigation" aria-label="Pagination Navigation">
        <p class="text-gray-500 dark:text-gray-400">
            Showing
            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $paginator->total() }}</span>
            results
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 items-center rounded-md border border-gray-200 px-3 text-gray-400 dark:border-gray-800 dark:text-gray-600">Previous</span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex h-9 items-center rounded-md border border-gray-200 px-3 font-medium text-gray-600 transition hover:bg-gray-50 hover:text-blue-600 disabled:opacity-60 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-blue-300"
                >
                    Previous
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-md px-2 text-gray-400 dark:text-gray-600">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-md bg-blue-600 px-3 font-semibold text-white shadow-sm" aria-current="page">{{ $page }}</span>
                        @else
                            <button
                                type="button"
                                wire:key="pagination-{{ $paginator->getPageName() }}-{{ $page }}"
                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                wire:loading.attr="disabled"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-gray-200 px-3 font-medium text-gray-600 transition hover:bg-gray-50 hover:text-blue-600 disabled:opacity-60 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-blue-300"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex h-9 items-center rounded-md border border-gray-200 px-3 font-medium text-gray-600 transition hover:bg-gray-50 hover:text-blue-600 disabled:opacity-60 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-blue-300"
                >
                    Next
                </button>
            @else
                <span class="inline-flex h-9 items-center rounded-md border border-gray-200 px-3 text-gray-400 dark:border-gray-800 dark:text-gray-600">Next</span>
            @endif
        </div>
    </nav>
@endif
