<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $default = [
            'Prospect',
            'Lead',
            'Hot Lead',
            'Cold Lead',
            'Customer',
            'VIP',
            'Partner',
            'Referral',
            'Churn Risk',
            'Opportunity',
        ];

        return [
            'name' => fake()->unique()->randomElement($default),
        ];
    }
}
