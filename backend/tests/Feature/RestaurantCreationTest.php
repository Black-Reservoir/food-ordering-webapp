<?php

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a restaurant owner can create a restaurant', function () {
    $user = User::factory()->restaurantOwner()->create([
        'email_verified_at' => now(),
    ]);

    $restaurant = Restaurant::factory()->make([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/restaurants', [
            'name' => $restaurant->name,
            'description' => $restaurant->description,
            'address' => $restaurant->address,
            'city' => $restaurant->city,
            'province' => $restaurant->province,
            'phone_number' => $restaurant->phone_number,
        ]);

    $this->assertDatabaseHas('restaurants', [
        'user_id' => $user->id,
        'name' => $restaurant->name,
        'is_active' => false,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Restaurant created successfully.')
        ->assertJsonPath('restaurant.name', $restaurant->name)
        ->assertJsonPath('restaurant.is_active', false);
});

test('a restaurant owner can create a restaurant with an image', function () {
    $user = User::factory()->restaurantOwner()->create([
        'email_verified_at' => now(),
    ]);

    Storage::fake('public');
    $image = UploadedFile::fake()->image('image.jpg');

    $restaurant = Restaurant::factory()->make([
        'user_id' => $user->id,
    ]);


    $response = $this->actingAs($user)
        ->postJson('/api/restaurants', [
            'name' => $restaurant->name,
            'description' => $restaurant->description,
            'address' => $restaurant->address,
            'city' => $restaurant->city,
            'province' => $restaurant->province,
            'phone_number' => $restaurant->phone_number,
            'image' => $image,
        ]);

    $createdRestaurant = Restaurant::where('user_id', $user->id)->latest('id')->firstOrFail();


    $this->assertDatabaseHas('restaurants', [
        'image' => $createdRestaurant->image,
        'user_id' => $user->id,
        'name' => $restaurant->name,
    ]);

    Storage::disk('public')->assertExists($createdRestaurant->image);
    $response->assertStatus(201)
        ->assertJsonPath('restaurant.image', fn($value) => is_string($value) && $value !== '');
});

test('a customer cannot create a restaurant', function () {
    $user = User::factory()->customer()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/restaurants');

    $this->assertDatabaseCount('restaurants', 0);

    $response->assertStatus(403);
});

test('unauthenticated user cannot create a restaurant', function () {
    $response = $this->postJson('/api/restaurants');

    $this->assertDatabaseCount('restaurants', 0);
    $response->assertStatus(401);
});

test('a restaurant owner cannot create restaurant if the validation fail', function () {
    $user = User::factory()->restaurantOwner()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/restaurants');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);

    $this->assertDatabaseCount('restaurants', 0);
});
