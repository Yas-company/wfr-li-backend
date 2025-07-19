<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Auth\Supplier;

use Tests\TestCase;
use App\Models\User;
use App\Models\Field;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierLoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_supplier_can_login()
    {
        $password = 'Password123!';
        $user = $this->createSupplier(['password' => Hash::make($password)]);

        $response = $this->postJson(route('auth.supplier.login'), [
            'phone' => $user->phone,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'phone', 'is_verified'],
                    'token',
                ],
                'message',
            ]);
    }

    public function test_supplier_cannot_login_with_invalid_credentials()
    {
        $user = $this->createSupplier();

        $response = $this->postJson(route('auth.supplier.login'), [
            'phone' => $user->phone,
            'password' => 'wrong',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_pending_supplier_cannot_login()
    {
        $password = 'Password123!';
        $user = User::factory()->create([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::PENDING,
            'is_verified' => true,
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(route('auth.supplier.login'), [
            'phone' => $user->phone,
            'password' => $password,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    // helpers
    protected function createSupplier(array $attributes = []): User
    {
        $supplier = User::factory()->create(array_merge([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ], $attributes));

        $supplier->fields()->attach(Field::factory()->create());

        return $supplier;
    }
}
