<?php

namespace Database\Factories;

use App\Models\Metric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Metric>
 */
class MetricFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group' => 'highlight',
            'label' => ucfirst(fake()->unique()->words(2, true)),
            'short_label' => fake()->word(),
            'value' => fake()->numberBetween(1, 500),
            'decimals' => 0,
            'suffix' => '+',
            'trend' => 'up',
            'color' => 'accent',
            'is_published' => true,
        ];
    }

    public function dashboard(): static
    {
        return $this->state(['group' => 'dashboard', 'badge' => 'YTD', 'note' => '+5% this month']);
    }
}
