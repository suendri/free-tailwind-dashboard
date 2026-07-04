<x-layouts.auth title="Login">
    <div class="mb-8">
        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Login akun</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Masuk ke dashboard</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Gunakan email dan password akun yang sudah terdaftar.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500"
                placeholder="admin@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-900 dark:text-white dark:focus:border-blue-500"
                placeholder="Password akun"
            >
            @error('password')
                <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
            <input
                name="remember"
                type="checkbox"
                value="1"
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900"
                @checked(old('remember'))
            >
            Ingat sesi login
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
            Daftar akun baru
        </a>
    </p>
</x-layouts.auth>
