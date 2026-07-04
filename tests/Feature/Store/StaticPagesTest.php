<?php

namespace Tests\Feature\Store;

use App\Mail\ContactMessageMail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_static_pages_render(): void
    {
        $this->get('/about')->assertOk()->assertSee('About');
        $this->get('/terms')->assertOk()->assertSee('Terms of Service');
        $this->get('/privacy')->assertOk()->assertSee('Privacy Policy');
        $this->get('/refund-policy')->assertOk()->assertSee('Refund Policy');
        $this->get('/contact')->assertOk()->assertSee('Contact Us');
    }

    public function test_contact_form_sends_the_message_to_support(): void
    {
        Mail::fake();
        Setting::current()->update(['support_email' => 'support@winnerdevs.com']);

        $this->post('/contact', [
            'name' => 'Interested Buyer',
            'email' => 'buyer@example.com',
            'subject' => 'Question about News Portal',
            'message' => 'Does it support Bangla content?',
        ])->assertRedirect(route('store.contact'))->assertSessionHas('success');

        Mail::assertSent(ContactMessageMail::class, function ($mail) {
            return $mail->hasTo('support@winnerdevs.com')
                && $mail->hasReplyTo('buyer@example.com')
                && $mail->senderName === 'Interested Buyer';
        });
    }

    public function test_honeypot_blocks_bots(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'subject' => 'Spam',
            'message' => 'Spam body',
            'website' => 'https://spam.example', // humans never see this field
        ])->assertSessionHasErrors('website');

        Mail::assertNothingSent();
    }
}
