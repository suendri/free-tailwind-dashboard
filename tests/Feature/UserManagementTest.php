<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_users_page(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_operator_cannot_open_users_page(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($operator)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_operator_does_not_see_users_menu(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($operator)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertDontSee('All Users')
            ->assertDontSee(route('users.index'));
    }

    public function test_admin_can_open_users_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
        User::factory()->create(['role' => 'operator', 'name' => 'Operator User']);
        User::factory()->create(['role' => 'operator', 'name' => 'Xavier']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response
            ->assertOk()
            ->assertSee('Search users')
            ->assertSee('Add New')
            ->assertSee('Admin User')
            ->assertSee('Operator User')
            ->assertSee('XA')
            ->assertSee('Delete User')
            ->assertDontSee("confirm('Delete this user?')", false);
    }

    public function test_admin_can_create_user_from_modal_component(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->set('name', 'New Admin')
            ->set('email', 'new-admin@example.test')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->set('role', 'admin')
            ->call('store')
            ->assertHasNoErrors();

        $user = User::query()->where('email', 'new-admin@example.test')->firstOrFail();

        $this->assertSame('New Admin', $user->name);
        $this->assertSame('admin', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Searchable Operator', 'email' => 'searchable@example.test']);
        User::factory()->create(['name' => 'Hidden Operator', 'email' => 'hidden@example.test']);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->set('search', 'Searchable')
            ->assertSee('Searchable Operator')
            ->assertDontSee('Hidden Operator');
    }

    public function test_admin_can_upload_user_photo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operator', 'photo' => null]);
        $photo = UploadedFile::fake()->image('avatar.jpg', 640, 640);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->call('openPhotoModal', $operator->id)
            ->set('photo', $photo)
            ->call('savePhoto')
            ->assertHasNoErrors();

        $operator->refresh();

        $this->assertNotNull($operator->photo);
        $this->assertStringEndsWith('.jpg', $operator->photo);
        $this->assertTrue(File::exists(public_path('uploads/user/'.$operator->photo)));

        File::delete(public_path('uploads/user/'.$operator->photo));
    }

    public function test_admin_email_change_requires_user_to_verify_the_new_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operator', 'email' => 'operator@example.test']);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->call('startEdit', $operator->id)
            ->set('name', 'Promoted User')
            ->set('email', 'promoted@example.test')
            ->set('password', 'newpassword')
            ->set('passwordConfirmation', 'newpassword')
            ->set('role', 'admin')
            ->call('update')
            ->assertHasNoErrors();

        $operator->refresh();

        $this->assertSame('Promoted User', $operator->name);
        $this->assertSame('promoted@example.test', $operator->email);
        $this->assertSame('admin', $operator->role);
        $this->assertTrue(Hash::check('newpassword', $operator->password));
        $this->assertNull($operator->email_verified_at);

        $this->actingAs($operator)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_admin_update_preserves_verification_when_email_does_not_change(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create([
            'role' => 'operator',
            'email' => 'operator@example.test',
        ]);
        $verifiedAt = $operator->email_verified_at;

        Livewire::actingAs($admin)
            ->test('users.index')
            ->call('startEdit', $operator->id)
            ->set('name', 'Renamed Operator')
            ->set('email', 'operator@example.test')
            ->set('role', 'operator')
            ->call('update')
            ->assertHasNoErrors();

        $operator->refresh();

        $this->assertSame('Renamed Operator', $operator->name);
        $this->assertNotNull($operator->email_verified_at);
        $this->assertTrue($verifiedAt->equalTo($operator->email_verified_at));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->call('delete', $admin->id)
            ->assertHasNoErrors()
            ->assertSee('bg-rose-100')
            ->assertSee('You cannot delete your own account.');

        $this->assertNotSoftDeleted((new User)->getTable(), [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_lower_own_account_to_operator(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->call('startEdit', $admin->id)
            ->set('role', 'operator')
            ->call('update')
            ->assertHasNoErrors()
            ->assertSee('bg-rose-100')
            ->assertSee('You cannot lower your own account to operator.');

        $this->assertDatabaseHas((new User)->getTable(), [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_soft_delete_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $operator = User::factory()->create(['role' => 'operator']);

        Livewire::actingAs($admin)
            ->test('users.index')
            ->call('delete', $operator->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted((new User)->getTable(), [
            'id' => $operator->id,
        ]);
    }
}
