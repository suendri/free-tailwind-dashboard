<x-layouts.auth title="Reset Password">
    <x-slot:aside>
        <p class="mb-4 text-sm font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-300">Password baru</p>
        <h1 class="text-4xl font-semibold leading-tight tracking-tight">
            Buat password baru untuk mengamankan akun Anda.
        </h1>
        <p class="mt-5 text-sm leading-6 text-slate-600 dark:text-gray-300">
            Gunakan password yang kuat dan berbeda dari password yang digunakan pada layanan lain.
        </p>
    </x-slot:aside>

    <div class="mb-8">
        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Pemulihan akun</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Atur password baru</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Masukkan email akun dan konfirmasi password baru Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="email"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password baru</label>
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
            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi password baru</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                placeholder="Ulangi password baru"
            >
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
            Simpan password baru
        </button>
    </form>
</x-layouts.auth>
