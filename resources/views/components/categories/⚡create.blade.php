<?php

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
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

    public function store(): void
    {
        $validated = $this->validate();

        Category::create($validated);

        session()->flash('status', 'Category created successfully.');

        $this->redirectRoute('categories.index');
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-950 dark:text-white">Add New Category</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Create a new category using the full page form. After saving, you will be redirected to All Category.
            </p>
        </div>

        <form wire:submit="store" class="space-y-5">
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category name</label>
                <input
                    id="name"
                    type="text"
                    wire:model="name"
                    autofocus
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Category name"
                >
                @error('name')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>
