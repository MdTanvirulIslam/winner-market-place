<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            // Orders are sales records — they must survive user or product
            // deletion, hence nullable FKs plus snapshot columns.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_slug'); // License Manager join key at time of sale
            $table->string('customer_name');
            $table->string('customer_email');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('BDT');
            $table->enum('status', ['pending', 'paid', 'delivered', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_method', 30)->default('manual'); // manual | sslcommerz (Phase 3)
            $table->string('sslcz_tran_id')->nullable();
            $table->string('sslcz_val_id')->nullable();
            // Filled by the License Manager provisioning call.
            $table->string('license_key')->nullable();
            $table->string('delivery_url', 500)->nullable();
            $table->enum('provisioning_status', ['none', 'provisioned', 'failed'])->default('none');
            $table->string('provisioning_error', 500)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('release_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('version', 50);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('settings', function (Blueprint $table) {
            // Shown to customers on pending orders (bKash / WhatsApp / bank
            // details) until SSLCommerz goes live in Phase 3.
            $table->text('payment_instructions')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('payment_instructions');
        });
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('orders');
    }
};
