<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $user = User::where('email', 'owner@example.com')->firstOrFail();

        return [
            'user_id' => User::factory()->restaurantOwner(), //Modified for testing, here's the old code: $user->id;
            'name' => fake()->company(),
            'description' => fake()->sentence(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'province' => 'Cavite',
            'phone_number' => fake()->phoneNumber(),
            'is_active' => false,
        ];
    }

    public function active(): Factory
    {
        return $this->state(function () {
            return [
                'is_active' => true,
            ];
        });
    }

    public function inactive(): Factory
    {
        return $this->state(function () {
            return [
                'is_active' => false,
            ];
        });
    }
}
