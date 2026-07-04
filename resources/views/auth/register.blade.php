<x-layouts.auth title="Register">
    <x-slot:aside>
        <p class="mb-4 text-sm font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">Operator access</p>
        <h1 class="text-4xl font-semibold leading-tight tracking-tight">
            Buat akun baru untuk mulai mengelola dashboard.
        </h1>
        <p class="mt-5 text-sm leading-6 text-slate-600 dark:text-gray-300">
            Akun registrasi otomatis masuk sebagai operator. Admin dapat mengubah role melalui menu Users setelah akun dibuat.
        </p>
        <div class="mt-8 grid gap-3">
            <div class="rounded-lg border border-sky-200 bg-white/70 p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-sm font-semibold">Default aman</p>
                <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-gray-400">Registrasi publik tidak langsung mendapat akses admin.</p>
            </div>
            <div class="rounded-lg border border-sky-200 bg-white/70 p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-sm font-semibold">Siap digunakan</p>
                <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-gray-400">Setelah registrasi berhasil, pengguna diarahkan ke dashboard sesuai konfigurasi Fortify.</p>
            </div>
        </div>
    </x-slot:aside>

    <div class="mb-8">
        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Registrasi akun</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Buat akun dashboard</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Akun baru akan dibuat sebagai operator.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama lengkap</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                placeholder="Nama user"
            >
            @error('name')
                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                placeholder="user@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Minimal 8 karakter"
                >
                @error('password')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Ulangi password"
                >
            </div>
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
            Daftar akun
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
            Masuk sekarang
        </a>
    </p>
</x-layouts.auth>
