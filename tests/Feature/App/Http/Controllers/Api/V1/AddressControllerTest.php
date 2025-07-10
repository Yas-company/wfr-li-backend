<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept-Language' => 'en'
        ]);

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        Address::factory()->count(3)->create(['user_id' => $this->user->id, 'is_default' => false]);
        Address::factory()->count(2)->create(['user_id' => $this->otherUser->id, 'is_default' => false]);
    }

    public function test_authenticated_user_can_get_their_addresses()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('addresses.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'street',
                        'city',
                        'phone',
                        'latitude',
                        'longitude',
                        'is_default',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ]
            ]);

        $response->assertJsonCount(3, 'data');
    }

    public function test_unauthenticated_user_cannot_get_addresses()
    {
        $response = $this->getJson(route('addresses.index'));

        $response->assertStatus(401);
    }

    public function test_user_can_create_new_address()
    {
        $addressData = [
            'name' => $this->faker->name,
            'street' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'phone' => $this->faker->phoneNumber,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'is_default' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('addresses.store'), $addressData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => $addressData
            ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'name' => $addressData['name'],
            'is_default' => true,
        ]);

        // Verify other addresses are no longer default
        $this->assertEquals(1, Address::where('user_id', $this->user->id)
            ->where('is_default', true)
            ->count());
    }

    public function test_user_can_create_non_default_address()
    {
        // First create a default address
        $defaultAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
        ]);

        $addressData = [
            'name' => $this->faker->name,
            'street' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'phone' => $this->faker->phoneNumber,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'is_default' => false,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('addresses.store'), $addressData);

        $response->assertStatus(201);

        // Verify the original default address is still default
        $this->assertTrue($defaultAddress->fresh()->is_default);
        $this->assertFalse(Address::latest('id')->first()->is_default);
    }

    public function test_address_creation_requires_all_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('addresses.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 'street', 'city', 'phone', 'latitude', 'longitude', 'is_default'
            ]);
    }

    public function test_user_can_update_their_address()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'is_default' => true,
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('addresses.update', $address->id), $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $address->id,
                    'name' => 'Updated Name',
                    'is_default' => true,
                ]
            ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'name' => 'Updated Name',
            'is_default' => true,
        ]);

        // Verify other addresses are no longer default
        $this->assertEquals(1, Address::where('user_id', $this->user->id)
            ->where('is_default', true)
            ->count());
    }

    public function test_user_cannot_update_another_users_address()
    {
        $otherUserAddress = Address::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('addresses.update', $otherUserAddress->id), $updateData);

        $response->assertStatus(404);
    }

    public function test_cannot_set_all_addresses_to_non_default()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
        ]);

        $updateData = [
            'is_default' => false,
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('addresses.update', $address->id), $updateData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'At least one default address is required',
            ]);
    }

    public function test_partial_update_works_without_changing_other_fields()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'city' => 'Original City',
            'is_default' => false,
        ]);

        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('addresses.update', $address->id), $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Name',
                    'city' => 'Original City',
                ]
            ]);
    }

    public function test_user_can_delete_their_address()
    {
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('addresses.destroy', $address->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_user_cannot_delete_another_users_address()
    {
        $otherUserAddress = Address::factory()->create([
            'user_id' => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('addresses.destroy', $otherUserAddress->id));

        $response->assertStatus(404);
    }

    public function test_cannot_delete_last_address()
    {
        $this->user->addresses()->delete();

        // Create only one address for the user
        $address = Address::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('addresses.destroy', $address->id));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete last address',
            ]);
    }

    public function test_deleting_default_address_makes_another_address_default()
    {

        $this->user->addresses()->delete();

        // Create two addresses - one default, one not
        $defaultAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
        ]);

        $otherAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('addresses.destroy', $defaultAddress->id));

        $response->assertStatus(200);

        // Verify the other address is now default
        $this->assertTrue($otherAddress->fresh()->is_default);
    }

    public function test_addresses_are_ordered_by_default_first_then_creation_date()
    {
        // Clear existing addresses
        $this->user->addresses()->delete();

        // Create addresses in specific order
        $oldest = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
            'created_at' => now()->subDays(2),
        ]);

        $newestNonDefault = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => false,
            'created_at' => now(),
        ]);

        $default = Address::factory()->create([
            'user_id' => $this->user->id,
            'is_default' => true,
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('addresses.index'));

        $response->assertStatus(200);

        $addresses = $response->json('data');
        $this->assertEquals($default['id'], $addresses[0]['id']);
        $this->assertEquals($newestNonDefault['id'], $addresses[1]['id']);
        $this->assertEquals($oldest['id'], $addresses[2]['id']);
    }
}
