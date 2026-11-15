<?php

use App\Models\Restaurant;

test("it should return active restaurants filtered by city", function () {

    $activeRestaurants = Restaurant::factory()
        ->active()
        ->count(3)
        ->sequence(
            ['name' => 'Burger House', 'city' => 'Imus'],
            ['name' => 'Pizza Corner', 'city' => 'Dasmariñas'],
            ['name' => 'Chicken Hub', 'city' => 'Imus'],
        )
        ->create();
    $inactiveRestaurant = Restaurant::factory()
        ->inactive()
        ->create(['name' => 'Sushi World', 'city' => 'Imus']);

    $response = $this->get('/api/restaurants?city=Imus');

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'id' => $activeRestaurants[0]->id,
            'name' => $activeRestaurants[0]->name,
            'city' => $activeRestaurants[0]->city,
        ]);

    $response->assertJsonFragment([
        'id' => $activeRestaurants[2]->id,
        'name' => $activeRestaurants[2]->name,
        'city' => $activeRestaurants[2]->city,
    ]);

    $response->assertJsonMissing([
        'id' => $activeRestaurants[1]->id,
        'name' => $activeRestaurants[1]->name,
        'city' => $activeRestaurants[1]->city,
    ]);

    $response->assertJsonMissing([
        'id' => $inactiveRestaurant->id,
        'name' => $inactiveRestaurant->name,
        'city' => $inactiveRestaurant->city,
    ], true);

    $this->assertDatabaseHas('restaurants', [
        'id' => $inactiveRestaurant->id,
        'name' => 'Sushi World',
    ]);
});

test('it should return restaurants without search parameter', function () {
    $activeRestaurants = Restaurant::factory()
        ->active()
        ->count(3)
        ->sequence(
            ['name' => 'Burger House'],
            ['name' => 'Pizza Corner'],
            ['name' => 'Chicken Hub'],
        )
        ->create();
    $inactiveRestaurant = Restaurant::factory()
        ->inactive()
        ->create(['name' => 'Sushi World']);

    $response = $this->get('/api/restaurants');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonMissing([
            'id' => $inactiveRestaurant->id,
            'name' => $inactiveRestaurant->name,
        ])
        ->assertJsonFragment([
            'id' => $activeRestaurants[0]->id,
            'name' => $activeRestaurants[0]->name,
        ])
        ->assertJsonFragment([
            'id' => $activeRestaurants[1]->id,
            'name' => $activeRestaurants[1]->name,
        ])
        ->assertJsonFragment([
            'id' => $activeRestaurants[2]->id,
            'name' => $activeRestaurants[2]->name,
        ])
        ->assertJsonPath('meta.total', 3);
});

test('it should return all active restaurant for empty search filter', function () {
    $activeRestaurants = Restaurant::factory()
        ->active()
        ->count(3)
        ->sequence(
            ['name' => 'Burger House'],
            ['name' => 'Pizza Corner'],
            ['name' => 'Chicken Hub'],
        )
        ->create();
    $inactiveRestaurant = Restaurant::factory()
        ->inactive()
        ->create(['name' => 'Sushi World']);

    $response = $this->get('/api/restaurants?search=');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonMissing([
            'id' => $inactiveRestaurant->id,
            'name' => $inactiveRestaurant->name,
        ])
        ->assertJsonFragment([
            'id' => $activeRestaurants[0]->id,
            'name' => $activeRestaurants[0]->name,
        ])
        ->assertJsonFragment([
            'id' => $activeRestaurants[1]->id,
            'name' => $activeRestaurants[1]->name,
        ])
        ->assertJsonFragment([
            'id' => $activeRestaurants[2]->id,
            'name' => $activeRestaurants[2]->name,
        ])
        ->assertJsonPath('meta.total', 3);
});

test('test combined filters', function () {
    $activeRestaurants = Restaurant::factory()
        ->active()
        ->count(3)
        ->sequence(
            ['name' => 'Burger House', 'city' => 'Imus'],
            ['name' => 'Burger King', 'city' => 'Dasmariñas'],
            ['name' => 'Pizza House', 'city' => 'Imus'],

        )
        ->create();
    $inactiveRestaurant = Restaurant::factory()
        ->inactive()
        ->create(['name' => 'Burger World', 'city' => 'Imus'],);

    $response = $this->get('/api/restaurants?city=Imus&search=Burger');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'id' => $activeRestaurants[0]->id,
            'name' => $activeRestaurants[0]->name,
        ])
        ->assertJsonMissing([
            'id' => $inactiveRestaurant->id,
            'name' => $inactiveRestaurant->name,
        ])
        ->assertJsonPath('meta.total', 1);
});

test('pagination first page', function () {
    Restaurant::factory()->active()->count(12)->create();
    $inactiveRestaurants = Restaurant::factory()->inactive()->count(2)->create();

    $response = $this->get('/api/restaurants');

    $response->assertStatus(200)->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 12)
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('links.next', fn($value) => $value !== null)
        ->assertJsonMissing([
            'id' => $inactiveRestaurants[0]->id,
            'name' => $inactiveRestaurants[0]->name,
        ])
        ->assertJsonMissing([
            'id' => $inactiveRestaurants[1]->id,
            'name' => $inactiveRestaurants[1]->name,
        ]);
});

test('pagination second page', function () {
    Restaurant::factory()->active()->count(12)->create();
    $inactiveRestaurants = Restaurant::factory()->inactive()->count(2)->create();

    $response = $this->get('/api/restaurants?page=2');

    $response->assertStatus(200)->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.total', 12)
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('links.next', null)
        ->assertJsonMissing([
            'id' => $inactiveRestaurants[0]->id,
            'name' => $inactiveRestaurants[0]->name,
        ])
        ->assertJsonMissing([
            'id' => $inactiveRestaurants[1]->id,
            'name' => $inactiveRestaurants[1]->name,
        ]);
});

test('pagination invalid page number', function () {
    Restaurant::factory()->active()->count(12)->create();

    $response = $this->get('/api/restaurants?page=99');

    $response->assertStatus(200)->assertJsonCount(0, 'data')
        ->assertJsonPath('meta.total', 12)
        ->assertJsonPath('meta.current_page', 99)
        ->assertJsonPath('links.next', null);
});

test('it should reject an invalid city parameter', function () {
    $response = $this->getJson('/api/restaurants?city[]=Imus');

    $response->assertStatus(422)->assertJsonValidationErrors(['city']);
});

test('it should reject an invalid search parameter', function () {
    $response = $this->getJson('/api/restaurants?city[]=Imus');

    $response->assertStatus(422)->assertJsonValidationErrors(['search']);
});

test('it should return restaurants sorted by creation date in descending order.', function () {
    Restaurant::factory()->active()
        ->count(3)
        ->sequence(
            ['name' => 'Burger King', 'created_at' => '2026-11-01'],
            ['name' => 'Sushi World', 'created_at' => '2026-11-03'],
            ['name' => 'Pizza Corner', 'created_at' => '2026-11-02'],
        )
        ->create();

    $response = $this->getJson('/api/restaurants');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.name', 'Sushi World')
        ->assertJsonPath('data.1.name', 'Pizza Corner')
        ->assertJsonPath('data.2.name', 'Burger King');
});

test('it should sort by id descending when creation dates are equal', function () {
    Restaurant::factory()->active()
        ->count(3)
        ->sequence(
            ['name' => 'Burger King', 'created_at' => '2026-11-01'],
            ['name' => 'Sushi World', 'created_at' => '2026-11-02'],
            ['name' => 'Pizza Corner', 'created_at' => '2026-11-02'],
        )
        ->create();

    $response = $this->getJson('/api/restaurants');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.name', 'Pizza Corner')
        ->assertJsonPath('data.1.name', 'Sushi World')
        ->assertJsonPath('data.2.name', 'Burger King');
});
