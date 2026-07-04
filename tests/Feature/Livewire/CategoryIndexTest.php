<?php

namespace Tests\Feature\Livewire;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_categories_page(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_categories_page(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['name' => 'News']);

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response
            ->assertOk()
            ->assertSee('All Category')
            ->assertSee('News')
            ->assertSee('Delete Category')
            ->assertDontSee("confirm('Delete this category?')", false);
    }

    public function test_guest_is_redirected_from_create_category_page(): void
    {
        $response = $this->get(route('categories.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_create_category_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.create'));

        $response
            ->assertOk()
            ->assertSee('Add New Category')
            ->assertSee('Save Category');
    }

    public function test_category_can_be_created(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('categories.index')
            ->set('name', 'Announcements')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas((new Category)->getTable(), [
            'name' => 'Announcements',
        ]);
    }

    public function test_categories_can_be_searched(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['name' => 'News Category']);
        Category::factory()->create(['name' => 'Finance Category']);

        Livewire::actingAs($user)
            ->test('categories.index')
            ->set('search', 'News')
            ->assertSee('News Category')
            ->assertDontSee('Finance Category');
    }

    public function test_category_pagination_has_active_state(): void
    {
        $user = User::factory()->create();
        Category::factory()->count(11)->create();

        Livewire::actingAs($user)
            ->test('categories.index')
            ->assertSee('bg-blue-600')
            ->assertSee('Next');
    }

    public function test_category_can_be_created_from_full_page_form(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('categories.create')
            ->set('name', 'Full Page Category')
            ->call('store')
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas((new Category)->getTable(), [
            'name' => 'Full Page Category',
        ]);
    }

    public function test_category_can_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($user)
            ->test('categories.index')
            ->call('startEdit', $category->id)
            ->set('name', 'Updated Name')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas((new Category)->getTable(), [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_category_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Delete Me']);

        Livewire::actingAs($user)
            ->test('categories.index')
            ->call('delete', $category->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing((new Category)->getTable(), [
            'id' => $category->id,
        ]);
    }

    public function test_category_with_posts_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Used Category']);
        Post::factory()->create(['category_id' => $category->id]);

        Livewire::actingAs($user)
            ->test('categories.index')
            ->call('delete', $category->id)
            ->assertHasNoErrors()
            ->assertSee('bg-rose-100')
            ->assertSee('Category cannot be deleted because it is still used by posts.');

        $this->assertDatabaseHas((new Category)->getTable(), [
            'id' => $category->id,
            'name' => 'Used Category',
        ]);
    }
}
