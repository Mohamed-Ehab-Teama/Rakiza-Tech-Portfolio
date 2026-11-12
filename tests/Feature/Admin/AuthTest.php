<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Signing in and out of the dashboard, and password resets.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/projects')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Sign in');
    }

    public function test_a_user_can_sign_in_and_out(): void
    {
        $user = $this->superAdmin(['email' => 'boss@rakiza.test', 'password' => 'secret-123']);

        $this->post('/admin/login', ['email' => 'boss@rakiza.test', 'password' => 'secret-123'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'signed_in']);

        $this->get('/admin')->assertOk()->assertSee('Welcome back');

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->superAdmin(['email' => 'boss@rakiza.test']);

        $this->from('/admin/login')
            ->post('/admin/login', ['email' => 'boss@rakiza.test', 'password' => 'nope'])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors(['email' => 'These details do not match an account.']);

        $this->assertGuest();
    }

    public function test_sign_in_is_locked_after_five_failed_attempts(): void
    {
        $this->superAdmin(['email' => 'boss@rakiza.test', 'password' => 'secret-123']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'boss@rakiza.test', 'password' => 'wrong']);
        }

        // Even the right password is refused while locked.
        $this->post('/admin/login', ['email' => 'boss@rakiza.test', 'password' => 'secret-123'])
            ->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many sign-in attempts', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_deactivated_users_cannot_sign_in(): void
    {
        $this->userWithRole('Editor', ['email' => 'off@rakiza.test', 'password' => 'secret-123', 'is_active' => false]);

        $this->post('/admin/login', ['email' => 'off@rakiza.test', 'password' => 'secret-123'])
            ->assertSessionHasErrors(['email' => 'This account is deactivated. Ask an administrator for access.']);

        $this->assertGuest();
    }

    public function test_a_user_switched_off_while_signed_in_is_signed_out(): void
    {
        $user = $this->userWithRole('Editor');
        $this->actingAs($user)->get('/admin')->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user->fresh())->get('/admin')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_forgot_password_sends_a_link_to_the_dashboard_reset_page(): void
    {
        Notification::fake();
        $user = $this->userWithRole('Editor', ['email' => 'forgot@rakiza.test']);

        $this->post('/admin/forgot-password', ['email' => 'forgot@rakiza.test'])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $url = $notification->toMail($user)->actionUrl;

            return str_contains($url, '/admin/reset-password/') && str_contains($url, urlencode('forgot@rakiza.test'));
        });
    }

    public function test_unknown_emails_get_the_same_answer(): void
    {
        Notification::fake();

        $this->post('/admin/forgot-password', ['email' => 'nobody@rakiza.test'])
            ->assertSessionHas('status', 'If an account exists for that email, a password reset link is on its way.');

        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = $this->userWithRole('Editor', ['email' => 'reset@rakiza.test']);
        $token = Password::createToken($user);

        $this->get("/admin/reset-password/{$token}?email=reset@rakiza.test")->assertOk()->assertSee('reset@rakiza.test');

        $this->post('/admin/reset-password', [
            'token' => $token,
            'email' => 'reset@rakiza.test',
            'password' => 'NewPass-2026',
            'password_confirmation' => 'NewPass-2026',
        ])->assertRedirect('/admin/login');

        $this->assertTrue(Hash::check('NewPass-2026', $user->fresh()->password));
    }

    public function test_weak_new_passwords_are_refused(): void
    {
        $user = $this->userWithRole('Editor', ['email' => 'reset@rakiza.test']);
        $token = Password::createToken($user);

        $this->post('/admin/reset-password', [
            'token' => $token,
            'email' => 'reset@rakiza.test',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    public function test_signing_in_is_logged_but_last_login_updates_are_not(): void
    {
        $user = $this->superAdmin(['email' => 'boss@rakiza.test', 'password' => 'secret-123']);

        $this->post('/admin/login', ['email' => 'boss@rakiza.test', 'password' => 'secret-123']);

        $this->assertSame(['signed_in'], ActivityLog::pluck('action')->all());
    }
}
