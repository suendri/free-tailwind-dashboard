<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_dashboard(): void
    {
        $user = User::factory()->create();
        Category::factory()->count(2)->create();
        Post::factory()->count(3)->create(['created_at' => now()]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Categories')
            ->assertSee('Posts')
            ->assertSee('Users')
            ->assertSee('Posts '.now()->year)
            ->assertSee('posts-year-chart')
            ->assertSee('data-posts-year-chart')
            ->assertDontSee('cdn.jsdelivr.net/npm/apexcharts');
    }
}
