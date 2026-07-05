<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('SAVE##??')),
            'type' => 'percent',
            'value' => 10,
            'expires_at' => null,
            'max_uses' => null,
            'used_count' => 0,
            'active' => true,
        ];
    }

    public function fixed(float $value): static
    {
        return $this->state(['type' => 'fixed', 'value' => $value]);
    }
}
