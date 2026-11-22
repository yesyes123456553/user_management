<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => fake()->unique()->numerify('2026-#####'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('09#########'),
            'dept_id' => Department::factory(),
            'course' => fake()->randomElement([
                'BS Information Technology',
                'BS Computer Science',
                'BS Information Systems',
            ]),
            'year_level' => fake()->numberBetween(1, 4),
            'status' => true,
        ];
    }
}