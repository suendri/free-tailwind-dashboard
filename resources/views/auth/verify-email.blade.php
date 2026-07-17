<x-layouts.auth title="Verifikasi Email">
    <x-slot:aside>
        <p class="mb-4 text-sm font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">Satu langkah lagi</p>
        <h1 class="text-4xl font-semibold leading-tight tracking-tight">
            Verifikasi email untuk mengaktifkan akses dashboard.
        </h1>
        <p class="mt-5 text-sm leading-6 text-slate-600 dark:text-gray-300">
            Langkah ini memastikan alamat email benar-benar dimiliki oleh pengguna yang mendaftar.
        </p>
    </x-slot:aside>

    <div class="mb-8">
        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Verifikasi email</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Periksa inbox Anda</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
            Kami telah mengirim tautan verifikasi ke <span class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->email }}</span>. Klik tautan tersebut untuk membuka dashboard.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            Tautan verifikasi baru telah dikirim ke email Anda.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
            Kirim ulang email verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
            Keluar dari akun
        </button>
    </form>
</x-layouts.auth>
