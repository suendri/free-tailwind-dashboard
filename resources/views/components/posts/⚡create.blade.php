<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $categoryId = '';

    public string $title = '';

    public string $text = '';

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'categoryId' => ['required', 'integer', Rule::exists('categories', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'categoryId' => 'category',
            'title' => 'post title',
            'text' => 'post text',
        ];
    }

    #[Computed]
    public function categoryOptions(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function store(): void
    {
        $validated = $this->validate();

        Post::create([
            'category_id' => (int) $validated['categoryId'],
            'title' => $validated['title'],
            'text' => $validated['text'] ?: null,
        ]);

        session()->flash('status', 'Post created successfully.');

        $this->redirectRoute('posts.index');
    }
};
?>

<div class="max-w-3xl space-y-6">
    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-950 dark:text-white">Add New Post</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Create a new post using the full page form. After saving, you will be redirected to All Posts.
            </p>
        </div>

        <form wire:submit="store" class="space-y-5">
            <div>
                <label for="category_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select
                    id="category_id"
                    wire:model="categoryId"
                    autofocus
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                >
                    <option value="">Select category</option>
                    @foreach ($this->categoryOptions as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                <input
                    id="title"
                    type="text"
                    wire:model="title"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Post title"
                >
                @error('title')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="text" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Text</label>
                <textarea
                    id="text"
                    wire:model="text"
                    rows="6"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Write post text"
                ></textarea>
                @error('text')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save Post
                </button>
            </div>
        </form>
    </div>
</div>
