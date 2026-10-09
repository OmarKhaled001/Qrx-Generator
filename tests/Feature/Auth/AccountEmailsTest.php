<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountEmailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_emails_are_sent_in_the_users_language(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['locale' => 'ar']);

        $user->sendEmailVerificationNotification();
        $user->sendPasswordResetNotification('token');

        Notification::assertSentTo($user, VerifyEmail::class, fn ($notification, $channels, $notifiable, $locale) => $locale === 'ar');
        Notification::assertSentTo($user, ResetPassword::class, fn ($notification, $channels, $notifiable, $locale) => $locale === 'ar');
    }

    public function test_the_arabic_verification_email_is_right_to_left_and_branded(): void
    {
        $user = User::factory()->unverified()->create(['locale' => 'ar']);
        $this->app->setLocale('ar');

        $mail = (new VerifyEmail)->toMail($user);
        $html = (string) $mail->render();

        $this->assertSame('أكّد عنوان بريدك الإلكتروني', $mail->subject);
        $this->assertStringContainsString('dir="rtl"', $html);
        $this->assertStringContainsString('brand/qrx-logo.png', $html);
        $this->assertStringContainsString('فريق Qrx', $html);
        $this->assertStringNotContainsString('Laravel', $html);
        $this->assertStringNotContainsString('Regards', $html);
    }

    public function test_the_english_reset_email_has_the_link_and_expiry(): void
    {
        $user = User::factory()->create(['locale' => 'en', 'email' => 'jane@example.com']);
        $this->app->setLocale('en');

        $mail = (new ResetPassword('secret-token'))->toMail($user);
        $html = (string) $mail->render();

        $this->assertSame('Reset your password', $mail->subject);
        $this->assertStringContainsString('dir="ltr"', $html);
        $this->assertStringContainsString('reset-password/secret-token?email=jane%40example.com', $html);
        $this->assertStringContainsString('This link expires in 60 minutes.', $html);
    }
}
