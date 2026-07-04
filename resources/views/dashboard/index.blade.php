<x-layouts.dashboard title="Dashboard" page-title="Dashboard" page-subtitle="Ringkasan awal aplikasi">
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 lg:col-span-2">
            <h2 class="mb-2 text-lg font-semibold">Konten Utama</h2>
            <p class="text-sm leading-6 text-gray-600 dark:text-gray-400">
                Layout dashboard sudah dipisah dari halaman ini. Nantinya halaman Livewire atau Blade lain cukup memakai
                <span class="font-medium text-gray-900 dark:text-gray-100">layouts.dashboard</span> agar mendapatkan sidebar,
                header, dark mode, dan area konten yang sama.
            </p>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-4 text-lg font-semibold">Status</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Laravel</dt>
                    <dd class="font-medium">13</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Livewire</dt>
                    <dd class="font-medium">4</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">UI</dt>
                    <dd class="font-medium">Tailwind</dd>
                </div>
            </dl>
        </section>
    </div>
</x-layouts.dashboard>
