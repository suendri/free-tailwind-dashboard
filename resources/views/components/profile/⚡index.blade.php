<?php

use App\Actions\Users\SaveUserPhoto;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';

    public ?TemporaryUploadedFile $photo = null;

    public function mount(): void
    {
        $this->name = $this->user()->name;
    }

    public function save(SaveUserPhoto $saveUserPhoto): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $this->user();
        $user->update(['name' => $validated['name']]);

        if ($validated['photo'] !== null) {
            $saveUserPhoto->execute($user, $validated['photo']);
        }

        session()->flash('status', 'Profile updated successfully.');
        $this->redirectRoute('profile.index');
    }

    public function initials(): string
    {
        $words = collect(preg_split('/\s+/', trim($this->name)) ?: [])
            ->filter()
            ->values();

        if ($words->isEmpty()) {
            return 'US';
        }

        if ($words->count() === 1) {
            return Str::upper(Str::substr($words->first(), 0, 2));
        }

        return Str::upper(Str::substr($words->first(), 0, 1).Str::substr($words->get(1), 0, 1));
    }

    public function photoUrl(): ?string
    {
        return $this->user()->photo ? asset('uploads/user/'.$this->user()->photo) : null;
    }

    private function user(): User
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
};
?>

<div class="max-w-4xl space-y-6">
    @if (session('status') && session('status') !== 'password-updated')
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="save" class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Profile information</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update the name and photo used across the dashboard.</p>
        </div>

        <div class="grid gap-8 p-6 md:grid-cols-[220px_minmax(0,1fr)]">
            <div class="flex flex-col items-center">
                <div class="flex h-40 w-40 items-center justify-center overflow-hidden rounded-xl bg-blue-50 text-4xl font-semibold text-blue-700 ring-1 ring-blue-100 dark:bg-blue-950 dark:text-blue-200 dark:ring-blue-900">
                    @if ($photo?->isPreviewable())
                        <img src="{{ $photo->temporaryUrl() }}" alt="Profile photo preview" class="h-full w-full object-cover">
                    @elseif ($this->photoUrl())
                        <img src="{{ $this->photoUrl() }}" alt="{{ $name }}" class="h-full w-full object-cover">
                    @else
                        <span>{{ $this->initials() }}</span>
                    @endif
                </div>

                <p class="mt-4 text-center text-xs leading-5 text-gray-500 dark:text-gray-400">
                    JPG, PNG, or WEBP.<br>1:1 ratio, Max 2 MB.
                </p>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="profile_photo" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Photo</label>
                    <input id="profile_photo" type="file" wire:model="photo" accept="image/jpeg,image/png,image/webp"
                        class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-900 outline-none transition file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:file:bg-gray-800 dark:file:text-gray-200 dark:hover:file:bg-gray-700 dark:focus:border-blue-500">
                    <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Preparing preview...
                    </div>
                    @error('photo')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input id="profile_name" type="text" wire:model="name" autocomplete="name"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile_email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="profile_email" type="email" value="{{ auth()->user()->email }}" disabled
                        class="block w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Email can only be changed by an administrator through the Users menu.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
            <button type="submit" wire:loading.attr="disabled" wire:target="photo,save"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20 disabled:cursor-not-allowed disabled:opacity-70">
                Save changes
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('user-password.update') }}" class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Change password</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use a strong password that you do not use on other services.</p>
        </div>

        <div class="space-y-5 p-6">
            @if (session('status') === 'password-updated')
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
                    Password updated successfully.
                </div>
            @endif

            <div>
                <label for="current_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500">
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="new_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">New password</label>
                    <input id="new_password" name="password" type="password" autocomplete="new-password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500">
                    @error('password', 'updatePassword')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20 disabled:cursor-not-allowed disabled:opacity-70">
                Update password
            </button>
        </div>
    </form>
</div>
