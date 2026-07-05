<?php

namespace Tests\Feature\Admin;

use App\Mail\NewReleaseMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReleaseNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function uploadRelease(Product $product, array $extra = []): void
    {
        $this->actingAs($this->staff())->post('/admin/releases', [
            'product_id' => $product->id,
            'version' => '2.0.0',
            'notes' => 'Big rewrite.',
            'file' => UploadedFile::fake()->create('app.zip', 100, 'application/zip'),
        ] + $extra);
    }

    public function test_delivered_buyers_are_emailed_about_a_new_release(): void
    {
        Mail::fake();
        Storage::fake('local');

        $product = Product::factory()->create();

        Order::factory()->delivered()->create(['product_id' => $product->id, 'customer_email' => 'alice@example.com']);
        Order::factory()->delivered()->create(['product_id' => $product->id, 'customer_email' => 'bob@example.com']);
        // Same buyer twice → only one email.
        Order::factory()->delivered()->create(['product_id' => $product->id, 'customer_email' => 'alice@example.com']);
        // Refunded and pending buyers are not notified.
        Order::factory()->create(['product_id' => $product->id, 'customer_email' => 'refunded@example.com', 'status' => 'refunded']);
        Order::factory()->create(['product_id' => $product->id, 'customer_email' => 'pending@example.com']);
        // Buyers of other products are not notified.
        Order::factory()->delivered()->create(['customer_email' => 'other@example.com']);

        $this->uploadRelease($product, ['notify_buyers' => 1]);

        Mail::assertSent(NewReleaseMail::class, 2);
        Mail::assertSent(NewReleaseMail::class, fn ($mail) => $mail->hasTo('alice@example.com'));
        Mail::assertSent(NewReleaseMail::class, fn ($mail) => $mail->hasTo('bob@example.com'));
        Mail::assertNotSent(NewReleaseMail::class, fn ($mail) => $mail->hasTo('refunded@example.com'));
        Mail::assertNotSent(NewReleaseMail::class, fn ($mail) => $mail->hasTo('pending@example.com'));
        Mail::assertNotSent(NewReleaseMail::class, fn ($mail) => $mail->hasTo('other@example.com'));
    }

    public function test_no_emails_go_out_when_the_box_is_unchecked(): void
    {
        Mail::fake();
        Storage::fake('local');

        $product = Product::factory()->create();
        Order::factory()->delivered()->create(['product_id' => $product->id]);

        $this->uploadRelease($product);

        Mail::assertNothingSent();
    }
}
