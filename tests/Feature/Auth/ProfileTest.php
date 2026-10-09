<?php

namespace Tests\Feature\Auth;

use App\Models\QrCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')->assertOk()->assertSee($user->email);
    }

    public function test_profile_information_can_be_updated_and_email_change_requires_verification(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->put('/user/profile-information', [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '+20 111 111 1111',
            'country' => 'Egypt',
            'city' => 'Giza',
            'area' => 'Dokki',
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('Giza', $user->city);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_is_kept_when_email_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/user/profile-information', ['name' => 'Same Email', 'email' => $user->email])->assertSessionHasNoErrors();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->put('/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }

    public function test_user_can_delete_their_account_and_qr_codes(): void
    {
        $user = User::factory()->create();
        $qrCode = QrCode::factory()->for($user)->create();

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertSessionHasNoErrors()->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
        $this->assertModelMissing($qrCode);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->delete('/profile', ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
