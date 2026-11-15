<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'first_name' => 'owner',
                'last_name' => 'example',
                'password' => Hash::make('password'),
            ],
        );
        $user->forceFill(['role' => 'restaurant_owner'])->save();

        User::factory()
            ->count(10)
            ->state(function () {
                return [
                    'role' => fake()->randomElement([
                        'customer',
                        'restaurant_owner',
                        'delivery_rider',
                        'admin',
                    ]),
                ];
            })
            ->create();
    }
}
