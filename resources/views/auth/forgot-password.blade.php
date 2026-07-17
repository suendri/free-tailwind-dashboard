<x-layouts.auth title="Lupa Password">
    <x-slot:aside>
        <p class="mb-4 text-sm font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-300">Pemulihan akun</p>
        <h1 class="text-4xl font-semibold leading-tight tracking-tight">
            Pulihkan akses akun dengan tautan yang aman.
        </h1>
        <p class="mt-5 text-sm leading-6 text-slate-600 dark:text-gray-300">
            Masukkan email yang terdaftar. Kami akan mengirim tautan untuk membuat password baru.
        </p>
    </x-slot:aside>

    <div class="mb-8">
        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Pemulihan akun</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Lupa password?</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">
            Tautan reset password akan dikirim ke alamat email akun Anda.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                placeholder="user@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
            Kirim tautan reset password
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Ingat password Anda?
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
            Kembali ke login
        </a>
    </p>
</x-layouts.auth>
