<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucwords($title),
            'role' => fake()->sentence(3),
            'client' => fake()->company(),
            'year' => fake()->numberBetween(2019, 2026),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(2, true),
            'tags' => ['Laravel', 'MySQL'],
            'visual' => fake()->randomElement(['dashboard', 'store', 'kanban', 'terminal', 'map']),
            'is_featured' => true,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false]);
    }
}
