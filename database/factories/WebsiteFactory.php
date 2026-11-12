<?php

namespace Database\Factories;

use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Website>
 */
class WebsiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'url' => 'https://'.fake()->unique()->domainName(),
            'client' => fake()->company(),
            'category' => fake()->randomElement(['Corporate', 'E-commerce', 'Clinic', 'Restaurant']),
            'description' => fake()->sentence(12),
            'status' => 'live',
            'launched_on' => fake()->dateTimeBetween('-3 years')->format('Y-m-d'),
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
