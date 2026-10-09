<?php

namespace Tests\Feature\Ui;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Laravel's own messages (validation, auth, password reset) are Arabic too.
 */
class ArabicMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_errors_are_arabic(): void
    {
        $this->withSession(['locale' => 'ar'])->from('/register')->post('/register', ['email' => 'not-an-email'])
            ->assertSessionHasErrors([
                'name' => 'حقل الاسم مطلوب.',
                'email' => 'حقل البريد الإلكتروني يجب أن يكون عنوان بريد إلكتروني صالحًا.',
            ]);
    }

    public function test_failed_login_and_password_reset_messages_are_arabic(): void
    {
        $user = User::factory()->create();

        $this->withSession(['locale' => 'ar'])->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors(['email' => 'بيانات الدخول هذه غير صحيحة.']);

        $this->withSession(['locale' => 'ar'])->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status', 'أرسلنا رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
    }

    public function test_the_admin_panel_renders_in_arabic(): void
    {
        $this->actingAs(User::factory()->admin()->create(['locale' => 'ar']))
            ->get('/admin')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('المستخدمون');
    }
}
