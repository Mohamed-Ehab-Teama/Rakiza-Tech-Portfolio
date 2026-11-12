<?php

namespace Tests\Feature;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * POST /contact — the website's contact form.
 */
class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private array $valid = [
        'name' => 'Sara Ali',
        'email' => 'sara@example.com',
        'message' => 'We need a booking website for our clinic.',
    ];

    public function test_a_valid_message_is_saved_and_answers_json(): void
    {
        Mail::fake();

        $this->postJson('/contact', $this->valid)
            ->assertOk()
            ->assertJsonFragment(['message' => "Message sent. We'll get back to you within 24h."]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sara@example.com',
            'read_at' => null,
        ]);
    }

    public function test_invalid_input_returns_field_errors(): void
    {
        $this->postJson('/contact', ['name' => '', 'email' => 'nope', 'message' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_honeypot_submissions_look_successful_but_are_not_saved(): void
    {
        $this->postJson('/contact', $this->valid + ['website' => 'http://spam.example'])
            ->assertOk();

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_form_without_javascript_redirects_back_to_the_contact_section(): void
    {
        Mail::fake();

        $this->post('/contact', $this->valid)
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHas('contact_status');

        $this->post('/contact', ['name' => '', 'email' => '', 'message' => ''])
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_the_form_is_rate_limited(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/contact', $this->valid)->assertOk();
        }

        $this->postJson('/contact', $this->valid)->assertStatus(429);
        $this->assertDatabaseCount('contact_messages', 5);
    }

    public function test_owner_is_emailed_when_notifications_are_on(): void
    {
        Mail::fake();
        app(Settings::class)->set(['contact_email' => 'owner@rakiza.example', 'notify_new_messages' => true]);

        $this->postJson('/contact', $this->valid)->assertOk();

        Mail::assertSent(NewContactMessage::class, function (NewContactMessage $mail) {
            return $mail->hasTo('owner@rakiza.example')
                && $mail->hasReplyTo('sara@example.com')
                && $mail->contactMessage->is(ContactMessage::first());
        });
    }

    public function test_no_email_when_notifications_are_off(): void
    {
        Mail::fake();
        app(Settings::class)->set(['notify_new_messages' => false]);

        $this->postJson('/contact', $this->valid)->assertOk();

        Mail::assertNothingSent();
        $this->assertDatabaseCount('contact_messages', 1);
    }
}
