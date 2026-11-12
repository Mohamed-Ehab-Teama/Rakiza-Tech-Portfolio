<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'skill_category_id' => SkillCategory::factory(),
            'name' => ucfirst(fake()->unique()->word()),
            'level' => null,
            'show_in_card' => true,
            'marquee_row' => 1,
            'is_published' => true,
        ];
    }
}
