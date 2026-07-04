<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response
            ->assertOk()
            ->assertSee('Registrasi akun')
            ->assertSee('Akun baru akan dibuat sebagai operator')
            ->assertDontSee('name="role"', false);
    }

    public function test_user_can_register_as_operator(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Operator User',
            'email' => 'operator@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas((new User)->getTable(), [
            'name' => 'Operator User',
            'email' => 'operator@example.com',
            'role' => 'operator',
        ]);
    }

    public function test_register_request_cannot_choose_admin_role(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Public User',
            'email' => 'public@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas((new User)->getTable(), [
            'email' => 'public@example.com',
            'role' => 'operator',
        ]);
    }
}
