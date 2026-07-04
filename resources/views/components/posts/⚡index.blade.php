<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $categoryId = '';

    public string $title = '';

    public string $text = '';

    public string $search = '';

    public ?int $editingPostId = null;

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
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->with('category:id,name')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query
                        ->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('text', 'like', '%'.$this->search.'%')
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categoryOptions(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function resetForm(): void
    {
        $this->reset(['categoryId', 'title', 'text', 'editingPostId']);
        $this->resetValidation();
    }

    public function store(): void
    {
        $validated = $this->validate();

        Post::create([
            'category_id' => (int) $validated['categoryId'],
            'title' => $validated['title'],
            'text' => $validated['text'] ?: null,
        ]);

        $this->resetForm();
        $this->resetPage();
        $this->dispatch('close-modal', name: 'post-create');
        session()->flash('status', 'Post created successfully.');
    }

    public function startEdit(int $postId): void
    {
        $post = Post::query()->findOrFail($postId);

        $this->editingPostId = $post->id;
        $this->categoryId = (string) $post->category_id;
        $this->title = $post->title;
        $this->text = $post->text ?? '';
        $this->resetValidation();
    }

    public function update(): void
    {
        $validated = $this->validate();

        Post::query()->findOrFail($this->editingPostId)->update([
            'category_id' => (int) $validated['categoryId'],
            'title' => $validated['title'],
            'text' => $validated['text'] ?: null,
        ]);

        $this->resetForm();
        $this->dispatch('close-modal', name: 'post-edit');
        session()->flash('status', 'Post updated successfully.');
    }

    public function delete(int $postId): void
    {
        Post::query()->findOrFail($postId)->delete();

        $this->resetPage();
        session()->flash('status', 'Post deleted successfully.');
    }
};
?>

<div
    x-data
    class="space-y-6"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-xs">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
            </svg>
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                placeholder="Search posts"
            >
        </div>

        <button
            type="button"
            x-on:click="$wire.resetForm().then(() => $dispatch('open-modal', 'post-create'))"
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

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($this->posts as $post)
                        <tr wire:key="post-{{ $post->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-950 dark:text-white">{{ $post->title }}</div>
                                @if ($post->text)
                                    <div class="mt-1 max-w-xl truncate text-xs text-gray-500 dark:text-gray-400">{{ $post->text }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $post->category?->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $post->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        x-on:click="$wire.startEdit({{ $post->id }}).then(() => $dispatch('open-modal', 'post-edit'))"
                                        class="rounded-md px-3 py-1.5 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                                    >
                                        Edit
                                    </button>
                                    <x-ui.confirm
                                        action="delete({{ $post->id }})"
                                        title="Delete Post"
                                        message="This post will be permanently removed. This action cannot be undone."
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
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ $search === '' ? 'No posts yet.' : 'No posts found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->posts->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                {{ $this->posts->links('components.ui.pagination') }}
            </div>
        @endif
    </section>

    <x-ui.modal name="post-create" title="Add New Post" description="Create a post and assign it to a category." max-width="2xl">
        <form wire:submit="store" class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="create_category_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select
                        id="create_category_id"
                        wire:model="categoryId"
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
                    <label for="create_title" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input
                        id="create_title"
                        type="text"
                        wire:model="title"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Post title"
                    >
                    @error('title')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="create_text" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Text</label>
                <textarea
                    id="create_text"
                    wire:model="text"
                    rows="5"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Write post text"
                ></textarea>
                @error('text')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'post-create')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Save
                </button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="post-edit" title="Edit Post" description="Update the selected post." max-width="2xl">
        <form wire:submit="update" class="space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="edit_category_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select
                        id="edit_category_id"
                        wire:model="categoryId"
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
                    <label for="edit_title" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input
                        id="edit_title"
                        type="text"
                        wire:model="title"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                        placeholder="Post title"
                    >
                    @error('title')
                        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="edit_text" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Text</label>
                <textarea
                    id="edit_text"
                    wire:model="text"
                    rows="5"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:focus:border-blue-500"
                    placeholder="Write post text"
                ></textarea>
                @error('text')
                    <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'post-edit')" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-600/20">
                    Update
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
