<?php

namespace Database\Factories;

use App\Enums\AttributeType;
use App\Models\Attribute;
use App\Models\Job;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobAttributeValue>
 */
class JobAttributeValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attribute = Attribute::inRandomOrder()->first();

        return [
            'job_id' => Job::factory(),
            'attribute_id' => $attribute->id,
            'value' => $this->getValueForAttribute($attribute),
        ];
    }

    private function getValueForAttribute(Attribute $attribute): string
    {
        return match($attribute->type) {
            AttributeType::Text->value => fake()->sentence(),
            AttributeType::Boolean->value => (int)fake()->boolean(),
            AttributeType::Number->value => fake()->numberBetween(1, 10),
            AttributeType::Date->value => fake()->date(),
            AttributeType::Select->value => fake()->randomElement($attribute->options),
            default => fake()->word(),
        };
    }
}
