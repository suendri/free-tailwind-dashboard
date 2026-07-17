<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $response = $this->get(route('profile.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_user_is_redirected_from_profile_page(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('profile.index'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_all_verified_users_can_open_profile_page_and_see_profile_menu(): void
    {
        foreach (['admin', 'operator'] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'email' => $role.'@example.test',
            ]);

            $response = $this->actingAs($user)->get(route('profile.index'));

            $response
                ->assertOk()
                ->assertSee('Profile')
                ->assertSee('Change password')
                ->assertSee(route('profile.index'))
                ->assertSee(route('user-password.update'))
                ->assertSee($user->email)
                ->assertSee('Email can only be changed by an administrator through the Users menu.')
                ->assertDontSee('mx-auto max-w-4xl', false)
                ->assertDontSee('wire:model="email"', false);
        }
    }

    public function test_user_can_update_own_name_without_changing_email_or_role(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'operator@example.test',
            'role' => 'operator',
        ]);

        Livewire::actingAs($user)
            ->test('profile.index')
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('profile.index'));

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('operator@example.test', $user->email);
        $this->assertSame('operator', $user->role);
    }

    public function test_user_cannot_change_own_email_through_fortify_profile_endpoint(): void
    {
        $user = User::factory()->create([
            'email' => 'operator@example.test',
        ]);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Updated Name',
            'email' => 'changed@example.test',
        ]);

        $response->assertNotFound();
        $this->assertSame('operator@example.test', $user->fresh()->email);
    }

    public function test_user_can_change_own_password_with_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile.index'))
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertRedirect(route('profile.index'))
            ->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_an_incorrect_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile.index'))
            ->put(route('user-password.update'), [
                'current_password' => 'incorrect-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertRedirect(route('profile.index'))
            ->assertSessionHasErrorsIn('updatePassword', ['current_password']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile.index'))
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'different-password',
            ]);

        $response
            ->assertRedirect(route('profile.index'))
            ->assertSessionHasErrorsIn('updatePassword', ['password']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_user_can_upload_square_profile_photo_and_old_photo_is_deleted(): void
    {
        $uploadPath = public_path('uploads/user');
        $oldPhoto = 'old-profile-photo.jpg';
        File::ensureDirectoryExists($uploadPath);
        File::put($uploadPath.DIRECTORY_SEPARATOR.$oldPhoto, 'old photo');

        $user = User::factory()->create(['photo' => $oldPhoto]);
        $photo = UploadedFile::fake()->image('profile.png', 640, 320)->size(500);

        Livewire::actingAs($user)
            ->test('profile.index')
            ->set('photo', $photo)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('profile.index'));

        $user->refresh();
        $savedPhotoPath = $uploadPath.DIRECTORY_SEPARATOR.$user->photo;

        $this->assertNotSame($oldPhoto, $user->photo);
        $this->assertStringEndsWith('.jpg', $user->photo);
        $this->assertFileExists($savedPhotoPath);
        $this->assertFileDoesNotExist($uploadPath.DIRECTORY_SEPARATOR.$oldPhoto);
        $this->assertSame([320, 320], array_slice(getimagesize($savedPhotoPath), 0, 2));

        File::delete($savedPhotoPath);
    }

    public function test_profile_photo_must_be_an_image_no_larger_than_two_megabytes(): void
    {
        $user = User::factory()->create(['photo' => null]);

        Livewire::actingAs($user)
            ->test('profile.index')
            ->set('photo', UploadedFile::fake()->create('profile.pdf', 100, 'application/pdf'))
            ->call('save')
            ->assertHasErrors(['photo' => 'image']);

        Livewire::actingAs($user)
            ->test('profile.index')
            ->set('photo', UploadedFile::fake()->image('profile.jpg')->size(2049))
            ->call('save')
            ->assertHasErrors(['photo' => 'max']);

        $this->assertNull($user->fresh()->photo);
    }
}
