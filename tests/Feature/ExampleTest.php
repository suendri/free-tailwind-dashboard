<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_and_login_route_render_the_same_login_page(): void
    {
        $homepageResponse = $this->get(route('home'));
        $loginResponse = $this->get(route('login'));

        $homepageResponse
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Masuk ke dashboard');
        $loginResponse
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Masuk ke dashboard');
    }

    public function test_authenticated_user_is_redirected_from_homepage_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertRedirect(route('dashboard'));
    }
}
