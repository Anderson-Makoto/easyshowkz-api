<?php

namespace Tests\Feature\E2E;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const USER_TABLE = 'users';

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function testRegisterUser(): void
    {
        $userData = $this->makeUserRegisterData();

        $response = $this->postJson('api/user/register', $userData)->assertOk();
        $responseData = $response->decodeResponseJson();

        $this->assertDatabaseCount($this::USER_TABLE, 1);

        $this->assertDatabaseHas($this::USER_TABLE, [
            'id' => $responseData['id'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email']
        ]);
    }

    public function testFailsToRegisterUserWithExistingEmail(): void
    {
        $user = User::factory()->create(['password' => '123123123']);

        $userData = $this->makeUserRegisterData([
            'email' => $user->email,
            'password' => '123123123'
        ]);

        $response = $this->postJson('api/user/register', $userData)->assertOk();
        $responseData = $response->decodeResponseJson();

        $this->assertDatabaseCount($this::USER_TABLE, 1);

        $this->assertDatabaseHas($this::USER_TABLE, [
            'id' => $responseData['id'],
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $userData['email']
        ]);
    }

    private function makeUserRegisterData(?array $data = null): array
    {
        $originalUserData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => $this->faker->text(8),
            'phone' => $this->faker->phoneNumber,
            'country_code' => $this->faker->randomElement(['1', '55', '591'])
        ];

        return $data ? array_merge($originalUserData, $data) : $originalUserData;
    }
}
