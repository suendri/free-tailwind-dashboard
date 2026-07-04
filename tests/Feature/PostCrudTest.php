<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_posts_page(): void
    {
        $response = $this->get(route('posts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_posts_page(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'News']);
        Post::factory()->create([
            'category_id' => $category->id,
            'title' => 'First Post',
        ]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response
            ->assertOk()
            ->assertSee('All Posts')
            ->assertSee('First Post')
            ->assertSee('News')
            ->assertSee('Delete Post')
            ->assertDontSee("confirm('Delete this post?')", false);
    }

    public function test_guest_is_redirected_from_create_post_page(): void
    {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_create_post_page(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['name' => 'News']);

        $response = $this->actingAs($user)->get(route('posts.create'));

        $response
            ->assertOk()
            ->assertSee('Add New Post')
            ->assertSee('Save Post')
            ->assertSee('News');
    }

    public function test_post_can_be_created(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test('posts.index')
            ->set('categoryId', (string) $category->id)
            ->set('title', 'Announcements')
            ->set('text', 'Post text')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas((new Post)->getTable(), [
            'category_id' => $category->id,
            'title' => 'Announcements',
            'text' => 'Post text',
        ]);
    }

    public function test_posts_can_be_searched(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['title' => 'Quarterly Report']);
        Post::factory()->create(['title' => 'Release Notes']);

        Livewire::actingAs($user)
            ->test('posts.index')
            ->set('search', 'Quarterly')
            ->assertSee('Quarterly Report')
            ->assertDontSee('Release Notes');
    }

    public function test_post_can_be_created_from_full_page_form(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test('posts.create')
            ->set('categoryId', (string) $category->id)
            ->set('title', 'Full Page Post')
            ->set('text', 'Full page text')
            ->call('store')
            ->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas((new Post)->getTable(), [
            'category_id' => $category->id,
            'title' => 'Full Page Post',
            'text' => 'Full page text',
        ]);
    }

    public function test_post_can_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create();
        $post = Post::factory()->create([
            'category_id' => $category->id,
            'title' => 'Old Title',
        ]);

        Livewire::actingAs($user)
            ->test('posts.index')
            ->call('startEdit', $post->id)
            ->set('categoryId', (string) $newCategory->id)
            ->set('title', 'Updated Title')
            ->set('text', 'Updated text')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas((new Post)->getTable(), [
            'id' => $post->id,
            'category_id' => $newCategory->id,
            'title' => 'Updated Title',
            'text' => 'Updated text',
        ]);
    }

    public function test_post_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Livewire::actingAs($user)
            ->test('posts.index')
            ->call('delete', $post->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing((new Post)->getTable(), [
            'id' => $post->id,
        ]);
    }
}
