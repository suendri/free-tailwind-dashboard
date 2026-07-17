<?php

use App\Actions\Users\SaveUserPhoto;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component {
    use WithFileUploads, WithPagination;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public string $role = 'operator';

    public string $search = '';

    public ?int $editingUserId = null;

    public ?TemporaryUploadedFile $photo = null;

    public ?int $photoUserId = null;

    public string $photoUserName = '';

    public ?string $photoUserPhoto = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    /**
     * @return array<string, mixed>
     */
    protected function createRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required', 'string'],
            'role' => ['required', Rule::in(['admin', 'operator'])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function updateRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->editingUserId)],
            'password' => ['nullable', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['admin', 'operator'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'password' => 'password',
            'passwordConfirmation' => 'password confirmation',
            'role' => 'role',
            'photo' => 'photo',
        ];
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('role', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function initials(string $name): string
    {
        $words = collect(preg_split('/\s+/', trim($name)) ?: [])
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

    public function photoUrl(?string $photo): ?string
    {
        return $photo ? asset('uploads/user/' . $photo) : null;
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'passwordConfirmation', 'editingUserId']);
        $this->role = 'operator';
        $this->resetValidation();
    }

    public function store(): void
    {
        $validated = $this->validate($this->createRules());

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        $this->resetForm();
        $this->resetPage();
        $this->dispatch('close-modal', name: 'user-create');
        session()->flash('status', 'User created successfully.');
    }

    public function startEdit(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->role = $user->role;
        $this->resetValidation();
    }

    public function update(): void
    {
        if ($this->editingUserId === null) {
            return;
        }

        $validated = $this->validate($this->updateRules());

        if ($this->editingUserId === auth()->id() && $validated['role'] !== 'admin') {
            $this->role = 'admin';
            session()->flash('error', 'You cannot lower your own account to operator.');

            return;
        }

        $user = User::query()->findOrFail($this->editingUserId);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (filled($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->forceFill(['email_verified_at' => null]);
        }

        $user->save();

        $this->resetForm();
        $this->dispatch('close-modal', name: 'user-edit');
        session()->flash('status', 'User updated successfully.');
    }

    public function delete(int $userId): void
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');

            return;
        }

        User::query()->findOrFail($userId)->delete();

        $this->resetPage();
        session()->flash('status', 'User deleted successfully.');
    }

    public function openPhotoModal(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->photoUserId = $user->id;
        $this->photoUserName = $user->name;
        $this->photoUserPhoto = $user->photo;
        $this->photo = null;
        $this->resetValidation('photo');
    }

    public function savePhoto(SaveUserPhoto $saveUserPhoto): void
    {
        if ($this->photoUserId === null) {
            return;
        }

        $validated = $this->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = User::query()->findOrFail($this->photoUserId);
        $fileName = $saveUserPhoto->execute($user, $validated['photo']);

        $this->photo = null;
        $this->photoUserPhoto = $fileName;
        $this->dispatch('close-modal', name: 'user-photo');
        session()->flash('status', 'User photo updated successfully.');
    }
};
?>

<div x-data class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-72">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
            </svg>
            <input type="search" wire:model.live.debounce.300ms="search"
                class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                placeholder="Search users">
        </div>

        <button type="button" x-on:click="$wire.resetForm().then(() => $dispatch('open-modal', 'user-create'))"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
            </svg>
            Add New
        </button>
    </div>

    @if (session('status'))
        <div
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div
            class="rounded-lg border border-rose-300 bg-rose-100 px-4 py-3 text-sm text-rose-800 dark:border-rose-500 dark:bg-rose-900 dark:text-rose-100">
            {{ session('error') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                <thead
                    class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Photo</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($this->users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <button type="button"
                                    x-on:click="$wire.openPhotoModal({{ $user->id }}).then(() => $dispatch('open-modal', 'user-photo'))"
                                    class="group flex h-11 w-11 items-center justify-center overflow-hidden rounded-lg bg-blue-50 text-sm font-semibold text-blue-700 ring-1 ring-blue-100 transition hover:ring-4 hover:ring-blue-600/15 dark:bg-blue-950 dark:text-blue-200 dark:ring-blue-900"
                                    aria-label="Upload photo for {{ $user->name }}">
                                    @if ($this->photoUrl($user->photo))
                                        <img src="{{ $this->photoUrl($user->photo) }}" alt="{{ $user->name }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <span>{{ $this->initials($user->name) }}</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-medium text-gray-950 dark:text-white">{{ $user->name }}</div>
                                    @if ($user->id === auth()->id())
                                        <span
                                            class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-950 dark:text-blue-300">Current
                                            account</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $user->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                        x-on:click="$wire.startEdit({{ $user->id }}).then(() => $dispatch('open-modal', 'user-edit'))"
                                        class="rounded-md px-3 py-1.5 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950">
                                        Edit
                                    </button>

                                    @if ($user->id === auth()->id())
                                        <button type="button" disabled
                                            class="cursor-not-allowed rounded-md px-3 py-1.5 text-sm font-medium text-gray-400 dark:text-gray-600">
                                            Delete
                                        </button>
                                    @else
                                        <x-ui.confirm action="delete({{ $user->id }})" title="Delete User"
                                            message="This user will be deactivated with soft delete and no longer appear in the user list."
                                            confirm-label="Delete">
                                            <x-slot:trigger>
                                                <button type="button"
                                                    class="rounded-md px-3 py-1.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950">
                                                    Delete
                                                </button>
                                            </x-slot:trigger>
                                        </x-ui.confirm>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ $search === '' ? 'No users yet.' : 'No users found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->users->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                {{ $this->users->links('components.ui.pagination') }}
            </div>
        @endif
    </section>

    <x-ui.modal name="user-create" title="Add New User" description="Create a user and assign the correct role."
        max-width="2xl">
        <form wire:submit="store" class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="create_name"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input id="create_name" type="text" wire:model="name"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Full name">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="create_email"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="create_email" type="email" wire:model="email"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="create_password"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input id="create_password" type="password" wire:model="password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Minimum 8 characters">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="create_password_confirmation"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <input id="create_password_confirmation" type="password" wire:model="passwordConfirmation"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Repeat password">
                    @error('passwordConfirmation')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="create_role"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select id="create_role" wire:model="role"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500">
                    <option value="operator">Operator</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close-modal', 'user-create')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="user-edit" title="Edit User" description="Update account details and role." max-width="2xl">
        <form wire:submit="update" class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="edit_name"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input id="edit_name" type="text" wire:model="name"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Full name">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit_email"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="edit_email" type="email" wire:model="email"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="edit_password"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input id="edit_password" type="password" wire:model="password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Leave blank to keep current password">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit_password_confirmation"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New
                        Password</label>
                    <input id="edit_password_confirmation" type="password" wire:model="passwordConfirmation"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Repeat new password">
                    @error('passwordConfirmation')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="edit_role"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select id="edit_role" wire:model="role" @disabled($editingUserId === auth()->id())
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500 dark:disabled:bg-gray-800">
                    <option value="operator">Operator</option>
                    <option value="admin">Admin</option>
                </select>
                @if ($editingUserId === auth()->id())
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Your own admin role cannot be lowered.</p>
                @endif
                @error('role')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close-modal', 'user-edit')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save Changes
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="user-photo" title="Update User Photo" description="Upload a square profile image."
        max-width="lg">
        <form wire:submit="savePhoto" class="space-y-5">
            <div class="flex flex-col items-center gap-4">
                <div
                    class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-xl bg-blue-50 text-3xl font-semibold text-blue-700 ring-1 ring-blue-100 dark:bg-blue-950 dark:text-blue-200 dark:ring-blue-900">
                    @if ($photo?->isPreviewable())
                        <img src="{{ $photo->temporaryUrl() }}" alt="Photo preview" class="h-full w-full object-cover">
                    @elseif ($photoUserPhoto !== null)
                        <img src="{{ $this->photoUrl($photoUserPhoto) }}" alt="{{ $photoUserName }}"
                            class="h-full w-full object-cover">
                    @else
                        <span>{{ $this->initials($photoUserName) }}</span>
                    @endif
                </div>

                <div class="text-center">
                    <div class="text-sm font-semibold text-gray-950 dark:text-white">{{ $photoUserName }}</div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, or WEBP. 1:1 ratio, Max 2 MB.</div>
                </div>
            </div>

            <div>
                <label for="user_photo" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Photo</label>
                <input id="user_photo" type="file" wire:model="photo" accept="image/*"
                    class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-900 outline-none transition file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:file:bg-gray-800 dark:file:text-gray-200 dark:hover:file:bg-gray-700 dark:focus:border-blue-500">
                <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Preparing preview...
                </div>
                @error('photo')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close-modal', 'user-photo')"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="photo,savePhoto"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20 disabled:cursor-not-allowed disabled:opacity-70">
                    Save Photo
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
