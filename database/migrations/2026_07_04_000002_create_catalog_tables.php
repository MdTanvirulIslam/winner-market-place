<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            // The join key with the License Manager — must match its product
            // slug exactly for license provisioning (Phase 2).
            $table->string('slug')->unique();
            $table->string('short_description', 500)->default('');
            $table->longText('description')->nullable();
            $table->text('features')->nullable();       // one feature per line
            $table->text('requirements')->nullable();   // e.g. PHP/MySQL versions
            $table->string('demo_url')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->text('notes')->nullable();
            // Stored on the private local disk (storage/app/private), never
            // under public/ — served only through authorized routes (Phase 2).
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
