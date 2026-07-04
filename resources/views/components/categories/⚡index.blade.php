<?php

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $name = '';

    public ?int $editingCategoryId = null;

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->editingCategoryId),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'category name',
        ];
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->latest()
            ->paginate(10);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingCategoryId']);
        $this->resetValidation();
    }

    public function store(): void
    {
        $validated = $this->validate();

        Category::create($validated);

        $this->resetForm();
        $this->dispatch('category-saved');
        session()->flash('status', 'Category created successfully.');
    }

    public function startEdit(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->resetValidation();
    }

    public function update(): void
    {
        $validated = $this->validate();

        Category::query()->findOrFail($this->editingCategoryId)->update($validated);

        $this->resetForm();
        $this->dispatch('category-saved');
        session()->flash('status', 'Category updated successfully.');
    }

    public function delete(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        if ($category->posts()->exists()) {
            session()->flash('error', 'Category cannot be deleted because it is still used by posts.');

            return;
        }

        $category->delete();

        $this->resetPage();
        session()->flash('status', 'Category deleted successfully.');
    }
};
?>

<div
    x-data
    x-on:category-saved.window="$dispatch('close-modal')"
    class="space-y-6"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-950 dark:text-white">All Category</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Create, edit, and organize the categories used by your content.
            </p>
        </div>

        <button
            type="button"
            x-on:click="$wire.resetForm().then(() => $dispatch('open-modal', 'category-create'))"
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
        <div class="rounded-lg border border-rose-300 bg-rose-100 px-4 py-3 text-sm font-medium text-rose-800 dark:border-rose-500 dark:bg-rose-900 dark:text-rose-100">
            {{ session('error') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($this->categories as $category)
                        <tr wire:key="category-{{ $category->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-950 dark:text-white">{{ $category->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $category->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        x-on:click="$wire.startEdit({{ $category->id }}).then(() => $dispatch('open-modal', 'category-edit'))"
                                        class="rounded-md px-3 py-1.5 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                                    >
                                        Edit
                                    </button>
                                    <x-ui.confirm
                                        action="delete({{ $category->id }})"
                                        title="Delete Category"
                                        message="This category will be permanently removed. This action cannot be undone."
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                No categories yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->categories->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                {{ $this->categories->links() }}
            </div>
        @endif
    </section>

    <x-ui.modal name="category-create" title="Add New Category" description="Create a category name for your content.">
        <form wire:submit="store" class="space-y-5">
            <div>
                <label for="create_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category name</label>
                <input
                    id="create_name"
                    type="text"
                    wire:model="name"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Category name"
                >
                @error('name')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'category-create')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="category-edit" title="Edit Category" description="Update the selected category name.">
        <form wire:submit="update" class="space-y-5">
            <div>
                <label for="edit_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category name</label>
                <input
                    id="edit_name"
                    type="text"
                    wire:model="name"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Category name"
                >
                @error('name')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'category-edit')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Update
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
