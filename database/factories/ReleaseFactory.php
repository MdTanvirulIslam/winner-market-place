<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Release>
 */
class ReleaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'version' => $this->faker->unique()->numerify('#.#.#'),
            'notes' => $this->faker->sentence(),
            'file_path' => 'releases/test/' . $this->faker->uuid() . '.zip',
            'file_size' => $this->faker->numberBetween(1_000_000, 50_000_000),
            'download_count' => 0,
            'released_at' => now(),
        ];
    }
}
