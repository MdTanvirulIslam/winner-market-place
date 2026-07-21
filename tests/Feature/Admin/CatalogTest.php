<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    public function test_customers_cannot_access_catalog_admin(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin/products')->assertForbidden();
        $this->actingAs($customer)->get('/admin/categories')->assertForbidden();
        $this->actingAs($customer)->get('/admin/releases')->assertForbidden();
    }

    public function test_staff_can_create_a_category(): void
    {
        $this->actingAs($this->staff())->post('/admin/categories', [
            'name' => 'News Portals',
            'slug' => 'news-portals',
            'description' => 'Publishing platforms.',
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['slug' => 'news-portals']);
    }

    public function test_invalid_slugs_are_rejected(): void
    {
        $this->actingAs($this->staff())->post('/admin/categories', [
            'name' => 'Bad Slug',
            'slug' => 'Bad Slug!',
        ])->assertSessionHasErrors('slug');
    }

    public function test_staff_can_create_a_product_with_screenshots(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->staff())->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'News Portal',
            'slug' => 'news-portal',
            'short_description' => 'A modern news publishing platform.',
            'description' => 'Full description here.',
            'features' => "Fast\nSecure",
            'requirements' => "PHP 8.2+",
            'price' => 15000,
            'sale_price' => 12000,
            'status' => 'published',
            'images' => [
                UploadedFile::fake()->image('shot1.png', 1280, 800),
                UploadedFile::fake()->image('shot2.png', 1280, 800),
            ],
        ]);

        $product = Product::where('slug', 'news-portal')->first();
        $this->assertNotNull($product);
        $response->assertRedirect(route('admin.products.edit', $product));

        $this->assertCount(2, $product->images);
        Storage::disk('public')->assertExists($product->images->first()->path);
    }

    public function test_sale_price_must_be_below_price(): void
    {
        $this->actingAs($this->staff())->post('/admin/products', [
            'name' => 'Overpriced Sale',
            'slug' => 'overpriced-sale',
            'short_description' => 'x',
            'price' => 100,
            'sale_price' => 150,
            'status' => 'draft',
        ])->assertSessionHasErrors('sale_price');
    }

    public function test_staff_can_upload_a_release_stored_on_the_private_disk(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create(['slug' => 'news-portal']);

        $this->actingAs($this->staff())->post('/admin/releases', [
            'product_id' => $product->id,
            'version' => '1.0.0',
            'notes' => 'Initial release.',
            'file' => UploadedFile::fake()->create('news-portal.zip', 2048, 'application/zip'),
        ])->assertRedirect(route('admin.releases.index'));

        $release = Release::where('product_id', $product->id)->first();
        $this->assertNotNull($release);
        $this->assertSame('releases/news-portal/news-portal-1.0.0.zip', $release->file_path);
        $this->assertSame(0, $release->download_count);

        // On the private local disk, not the public one.
        Storage::disk('local')->assertExists($release->file_path);
    }

    public function test_ajax_release_upload_returns_json_redirect(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create(['slug' => 'ajax-product']);

        // Success: JSON body carries the redirect target for the uploader.
        $this->actingAs($this->staff())->postJson('/admin/releases', [
            'product_id' => $product->id,
            'version' => '1.0.0',
            'notes' => '<p>First release.</p>',
            'file' => UploadedFile::fake()->create('app.zip', 100, 'application/zip'),
        ])->assertOk()
            ->assertJson(['redirect' => route('admin.releases.index')]);

        $this->assertNotNull(Release::where('product_id', $product->id)->first());

        // Validation failure: 422 JSON the uploader can display inline.
        $this->actingAs($this->staff())->postJson('/admin/releases', [
            'product_id' => $product->id,
            'version' => 'not a version!!',
            'file' => UploadedFile::fake()->create('app.zip', 100, 'application/zip'),
        ])->assertStatus(422)
            ->assertJsonValidationErrors('version');
    }

    public function test_duplicate_versions_per_product_are_rejected(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create();
        Release::factory()->create(['product_id' => $product->id, 'version' => '1.0.0']);

        $this->actingAs($this->staff())->post('/admin/releases', [
            'product_id' => $product->id,
            'version' => '1.0.0',
            'file' => UploadedFile::fake()->create('app.zip', 100, 'application/zip'),
        ])->assertSessionHasErrors('version');
    }

    public function test_non_zip_release_uploads_are_rejected(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create();

        $this->actingAs($this->staff())->post('/admin/releases', [
            'product_id' => $product->id,
            'version' => '1.0.0',
            'file' => UploadedFile::fake()->create('app.exe', 100, 'application/octet-stream'),
        ])->assertSessionHasErrors('file');
    }

    public function test_short_description_length_counts_text_not_markup(): void
    {
        $staff = $this->staff();
        $base = [
            'name' => 'Rich Product',
            'slug' => 'rich-product',
            'price' => 100,
            'status' => 'draft',
        ];

        // 600 characters of visible text is too long even inside HTML.
        $this->actingAs($staff)->post('/admin/products', $base + [
            'short_description' => '<p>' . str_repeat('a', 600) . '</p>',
        ])->assertSessionHasErrors('short_description');

        // 300 characters wrapped in markup is fine.
        $this->actingAs($staff)->post('/admin/products', $base + [
            'short_description' => '<p><strong>' . str_repeat('b', 300) . '</strong></p>',
        ])->assertSessionDoesntHaveErrors('short_description');
    }

    public function test_failed_php_upload_reports_the_real_reason(): void
    {
        Storage::fake('public');
        $staff = $this->staff();
        $product = Product::factory()->create();

        // Simulate PHP dropping the file for exceeding upload_max_filesize.
        $broken = new UploadedFile(
            UploadedFile::fake()->image('shot.png')->getPathname(),
            'shot.png',
            'image/png',
            UPLOAD_ERR_INI_SIZE,
            true
        );

        $response = $this->actingAs($staff)->patch(route('admin.products.update', $product), [
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'price' => $product->price,
            'status' => $product->status,
            'images' => [$broken],
        ]);

        $response->assertSessionHasErrors('images.0');
        $this->assertStringContainsString('upload_max_filesize', session('errors')->first('images.0'));
        $this->assertSame(0, $product->images()->count());
    }

    public function test_deleting_a_screenshot_removes_the_file(): void
    {
        Storage::fake('public');
        $staff = $this->staff();
        $product = Product::factory()->create();

        $this->actingAs($staff)->patch(route('admin.products.update', $product), [
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'price' => $product->price,
            'status' => $product->status,
            'images' => [UploadedFile::fake()->image('shot.png')],
        ]);

        $image = $product->images()->first();
        $this->assertNotNull($image);

        $this->actingAs($staff)->delete(route('admin.products.images.destroy', [$product, $image]));

        Storage::disk('public')->assertMissing($image->path);
        $this->assertNull($image->fresh());
    }

    public function test_uploaded_screenshots_are_normalized_to_the_gallery_size(): void
    {
        Storage::fake('public');

        $this->actingAs($this->staff())->post('/admin/products', [
            'name' => 'Wide Banner',
            'slug' => 'wide-banner',
            'short_description' => 'Ships with an ultra-wide screenshot.',
            'price' => 100,
            'status' => 'draft',
            'images' => [UploadedFile::fake()->image('banner.png', 3000, 800)],
        ]);

        $product = Product::where('slug', 'wide-banner')->firstOrFail();
        $image = $product->images()->first();
        $this->assertNotNull($image);
        // Everything is stored as WebP, named "<domain>_<unique>.webp".
        $this->assertStringEndsWith('.webp', $image->path);
        $this->assertStringStartsWith('localhost_', basename($image->path));

        $size = getimagesizefromstring(Storage::disk('public')->get($image->path));
        $this->assertSame([\App\Support\Screenshot::WIDTH, \App\Support\Screenshot::HEIGHT], [$size[0], $size[1]]);
    }

    public function test_normalize_command_fixes_existing_screenshots_and_is_idempotent(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();

        // An odd-shaped screenshot stored before upload processing existed.
        $path = 'products/' . $product->id . '/legacy-banner.jpg';
        Storage::disk('public')->put($path, UploadedFile::fake()->image('legacy-banner.jpg', 1280, 456)->get());
        $image = $product->images()->create(['path' => $path, 'sort_order' => 1]);

        $this->artisan('screenshots:normalize')->assertSuccessful();

        // The command re-encodes to WebP and renames the legacy .jpg file.
        $image->refresh();
        $this->assertStringEndsWith('.webp', $image->path);
        Storage::disk('public')->assertMissing($path);

        $size = getimagesizefromstring(Storage::disk('public')->get($image->path));
        $this->assertSame([\App\Support\Screenshot::WIDTH, \App\Support\Screenshot::HEIGHT], [$size[0], $size[1]]);

        $this->artisan('screenshots:normalize')
            ->expectsOutputToContain('skipped 1')
            ->assertSuccessful();
    }
}
