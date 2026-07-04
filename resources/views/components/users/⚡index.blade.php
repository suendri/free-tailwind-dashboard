<?php

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public string $role = 'operator';

    public ?int $editingUserId = null;

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
        ];
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->latest()
            ->paginate(10);
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

        $user->update($data);

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
};
?>

<div
    x-data
    class="space-y-6"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <button
            type="button"
            x-on:click="$wire.resetForm().then(() => $dispatch('open-modal', 'user-create'))"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
            </svg>
            Add New
        </button>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-rose-300 bg-rose-100 px-4 py-3 text-sm text-rose-800 dark:border-rose-500 dark:bg-rose-900 dark:text-rose-100">
            {{ session('error') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                    <tr>
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
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-medium text-gray-950 dark:text-white">{{ $user->name }}</div>
                                    @if ($user->id === auth()->id())
                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-950 dark:text-blue-300">Current account</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $user->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        x-on:click="$wire.startEdit({{ $user->id }}).then(() => $dispatch('open-modal', 'user-edit'))"
                                        class="rounded-md px-3 py-1.5 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                                    >
                                        Edit
                                    </button>

                                    @if ($user->id === auth()->id())
                                        <button
                                            type="button"
                                            disabled
                                            class="cursor-not-allowed rounded-md px-3 py-1.5 text-sm font-medium text-gray-400 dark:text-gray-600"
                                        >
                                            Delete
                                        </button>
                                    @else
                                        <x-ui.confirm
                                            action="delete({{ $user->id }})"
                                            title="Delete User"
                                            message="This user will be deactivated with soft delete and no longer appear in the user list."
                                            confirm-label="Delete"
                                        >
                                            <x-slot:trigger>
                                                <button
                                                    type="button"
                                                    class="rounded-md px-3 py-1.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950"
                                                >
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
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                No users yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->users->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                {{ $this->users->links() }}
            </div>
        @endif
    </section>

    <x-ui.modal name="user-create" title="Add New User" description="Create a user and assign the correct role." max-width="2xl">
        <form wire:submit="store" class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="create_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input
                        id="create_name"
                        type="text"
                        wire:model="name"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Full name"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="create_email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input
                        id="create_email"
                        type="email"
                        wire:model="email"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="email@example.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="create_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input
                        id="create_password"
                        type="password"
                        wire:model="password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Minimum 8 characters"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="create_password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <input
                        id="create_password_confirmation"
                        type="password"
                        wire:model="passwordConfirmation"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Repeat password"
                    >
                    @error('passwordConfirmation')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="create_role" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select
                    id="create_role"
                    wire:model="role"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                >
                    <option value="operator">Operator</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close-modal', 'user-create')" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="user-edit" title="Edit User" description="Update account details and role." max-width="2xl">
        <form wire:submit="update" class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="edit_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input
                        id="edit_name"
                        type="text"
                        wire:model="name"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Full name"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit_email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input
                        id="edit_email"
                        type="email"
                        wire:model="email"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="email@example.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="edit_password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input
                        id="edit_password"
                        type="password"
                        wire:model="password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Leave blank to keep current password"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="edit_password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                    <input
                        id="edit_password_confirmation"
                        type="password"
                        wire:model="passwordConfirmation"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Repeat new password"
                    >
                    @error('passwordConfirmation')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="edit_role" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select
                    id="edit_role"
                    wire:model="role"
                    @disabled($editingUserId === auth()->id())
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500 dark:disabled:bg-gray-800"
                >
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
                <button type="button" x-on:click="$dispatch('close-modal', 'user-edit')" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save Changes
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
