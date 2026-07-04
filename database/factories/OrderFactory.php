<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_no' => 'WM-' . now()->year . '-' . $this->faker->unique()->numerify('######'),
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'product_name' => 'Test Product',
            'product_slug' => 'test-product',
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->unique()->safeEmail(),
            'amount' => 10000,
            'currency' => 'BDT',
            'status' => 'pending',
            'payment_method' => 'manual',
        ];
    }

    public function configure(): static
    {
        // Keep the snapshot columns consistent with the related product.
        return $this->afterCreating(function ($order) {
            if ($order->product) {
                $order->update([
                    'product_name' => $order->product->name,
                    'product_slug' => $order->product->slug,
                ]);
            }
        });
    }

    public function paid(): static
    {
        return $this->state(['status' => 'paid', 'paid_at' => now()]);
    }

    public function delivered(): static
    {
        return $this->state([
            'status' => 'delivered',
            'paid_at' => now(),
            'delivered_at' => now(),
            'license_key' => 'TEST-LICENSE-KEY-1234',
            'provisioning_status' => 'provisioned',
        ]);
    }
}
