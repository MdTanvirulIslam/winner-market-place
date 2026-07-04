<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->words(3, true));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'short_description' => $this->faker->sentence(12),
            'description' => $this->faker->paragraphs(3, true),
            'features' => "Responsive design\nAdmin dashboard\nRole management",
            'requirements' => "PHP 8.2+\nMySQL 5.7+",
            'demo_url' => null,
            'price' => $this->faker->numberBetween(50, 500) * 100,
            'sale_price' => null,
            'status' => 'draft',
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }

    public function onSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'sale_price' => round(((float) $attributes['price']) * 0.7, 2),
        ]);
    }
}
