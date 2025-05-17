<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $industries = [
            'Technology',
            'Healthcare',
            'Finance',
            'Retail',
            'Manufacturing',
            'Education',
            'Hospitality',
            'Construction',
            'Consulting',
            'Transportation',
        ];

        return [
            'name' => fake()->company(),
            'industry' => fake()->randomElement($industries),
            'website' => fake()->optional()->url(),
        ];
    }
}
