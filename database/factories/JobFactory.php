<?php

namespace Database\Factories;

use App\Enums\JobStatus;
use App\Enums\JobType;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(JobStatus::values());

        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'company_name' => fake()->company(),
            'salary_min' => fake()->numberBetween(30000, 80000),
            'salary_max' => fake()->numberBetween(80000, 200000),
            'is_remote' => fake()->boolean(),
            'job_type' => fake()->randomElement(JobType::values()),
            'status' => $status,
            'published_at' => $status == 'published' ? now() : null,
        ];
    }
}
