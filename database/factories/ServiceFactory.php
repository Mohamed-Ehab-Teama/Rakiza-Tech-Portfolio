<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->word()),
            'description' => fake()->sentence(12),
            'visual' => fake()->randomElement(array_keys(Service::VISUALS)),
            'tags' => ['REST', 'Auth'],
            'is_wide' => false,
            'is_published' => true,
        ];
    }
}
