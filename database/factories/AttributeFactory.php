<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['text', 'number', 'boolean', 'date', 'select']);

        $selectOptions = [
            ['Low', 'Medium', 'High', 'Critical', 'Urgent', 'Normal'],
            ['Red', 'Green', 'Blue', 'Yellow', 'Black', 'White', 'Orange', 'Purple'],
            ['Yes', 'No', 'Maybe', 'Approved', 'Rejected', 'Pending', 'Cancelled'],
            ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ['North', 'South', 'East', 'West'],
            ['Active', 'Inactive', 'Enabled', 'Disabled', 'On', 'Off', 'Open', 'Closed'],
            ['Small', 'Medium', 'Large', 'Extra Large', 'XXL'],
            ['Paid', 'Unpaid', 'Refunded', 'Processing'],
            ['Success', 'Error', 'Warning', 'Info', 'Debug', 'Pending', 'Completed'],
            ['Sunny', 'Rainy', 'Cloudy', 'Snowy', 'Windy', 'Stormy'],
            ['Happy', 'Sad', 'Angry', 'Excited', 'Bored', 'Tired']
        ];

        return [
            'name' => $this->faker->unique()->word(),
            'type' => $type,
            'options' => $type == 'select' ? $this->faker->randomElement($selectOptions) : null
        ];
    }
}
