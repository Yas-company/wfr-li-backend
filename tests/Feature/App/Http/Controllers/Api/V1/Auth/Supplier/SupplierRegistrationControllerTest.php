<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Auth\Supplier;

use Tests\TestCase;
use App\Models\User;
use App\Models\Field;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierRegistrationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_supplier_can_register()
    {
        $field = Field::factory()->create();
        $data = $this->getValidRegistrationData(UserRole::SUPPLIER->value);
        $data['fields'] = [$field->id];

        $response = $this->postJson(route('auth.supplier.register'), $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id', 'name', 'phone', 'email', 'role', 'is_verified',
                    ],
                    'message',
                ],
            ])
            ->assertJsonPath('data.user.role', UserRole::SUPPLIER->label())
            ->assertJsonPath('data.user.is_verified', false);

        $user = User::where('phone', $data['phone'])->first();
        $this->assertEquals(UserStatus::PENDING, $user->status);
        $this->assertCount(1, $user->fields);
        Storage::disk('public')->assertExists($user->license_attachment);
        Storage::disk('public')->assertExists($user->commercial_register_attachment);
    }

        public function test_cannot_register_with_invalid_password()
    {
        $data = $this->getValidRegistrationData();
        $data['password'] = 'weak';
        $data['password_confirmation'] = 'weak';

        $response = $this->postJson(route('auth.supplier.register'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_supplier_must_provide_required_documents()
    {
        $data = $this->getValidRegistrationData(UserRole::SUPPLIER->value);
        unset($data['license_attachment'], $data['commercial_register_attachment']);

        $response = $this->postJson(route('auth.supplier.register'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'license_attachment',
                'commercial_register_attachment',
            ]);
    }

    public function test_pending_supplier_updates_email_instead_of_registering()
    {
        $pendingSupplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::PENDING,
            'is_verified' => false,
            'phone' => '966501234567',
            'email' => 'test@example.com',
        ]);

        $data1 = $this->getValidRegistrationData(UserRole::SUPPLIER->value);
        $data1['phone'] = $pendingSupplier->phone;
        $data1['email'] = 'newemail@example.com';

        $response1 = $this->postJson(route('auth.supplier.register'), $data1);
        $response1->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'phone' => $pendingSupplier->phone,
            'email' => 'newemail@example.com',
            'status' => UserStatus::PENDING,
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_pending_supplier_cannot_register_with_same_email()
    {
        $pendingSupplier = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::PENDING,
            'is_verified' => false,
            'phone' => '966501234567',
            'email' => 'test@example.com',
        ]);

        $data1 = $this->getValidRegistrationData(UserRole::SUPPLIER->value);
        $data1['phone'] = '966501234566';
        $data1['email'] = $pendingSupplier->email;

        $response1 = $this->postJson(route('auth.supplier.register'), $data1);
        $response1->assertStatus(500);

        $this->assertDatabaseCount('users', 1);
    }

    // helpers

    protected function getValidRegistrationData(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => '966'.$this->faker->numerify('########'),
            'country_code' => '966',
            'business_name' => $this->faker->company,
            'email' => $this->faker->safeEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'address' => [
                'name' => $this->faker->word,
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'phone' => '966'.$this->faker->numerify('########'),
                'latitude' => $this->faker->latitude,
                'longitude' => $this->faker->longitude,
            ],
            'license_attachment' => UploadedFile::fake()->create('license.pdf', 1000),
            'commercial_register_attachment' => UploadedFile::fake()->create('commercial.pdf', 1000),
            'fields' => [Field::factory()->create()->id],
        ];
    }
}
