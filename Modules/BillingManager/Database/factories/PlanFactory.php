<?php

declare(strict_types=1);

namespace Modules\BillingManager\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\BillingManager\App\Models\Plan;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->unique()->slug(),
            'price' => $this->faker->numberBetween(500, 2000),
            'currency' => 'usd',
            'stripe_price_id' => 'price_'.$this->faker->unique()->word(),
        ];
    }
}
