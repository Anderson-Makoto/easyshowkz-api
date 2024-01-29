<?php

namespace Tests\Feature\E2E;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Helpers\PhoneNumberHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'email' => $userData['email'],
            'phone' => $userData['phone'],
        ]);
    }

    public function testFailsToRegisterIfInvalidEmailShouldReturnError()
    {
        $userData = $this->makeUserRegisterData(['email' => 'invalid_email']);

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The email must be a valid email address.', json_decode($responseData->json)->errors->email[0]);

        $userData['email'] = null;

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The email field is required.', json_decode($responseData->json)->errors->email[0]);

        $user = User::factory()->create();
        $userData['email'] = $user->email;

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The email has already been taken.', json_decode($responseData->json)->errors->email[0]);
    }

    public function testFailsToRegisterIfInvalidPhoneShouldReturnError()
    {
        $userData = $this->makeUserRegisterData(['phone' => 'a123456789']);

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The phone format is invalid.', json_decode($responseData->json)->errors->phone[0]);

        $userData['phone'] = null;

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The phone field is required.', json_decode($responseData->json)->errors->phone[0]);

        $userData['phone'] = '11';

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The phone format is invalid.', json_decode($responseData->json)->errors->phone[0]);

        $userData['phone'] = '111111111111111111111';

        $response = $this->postJson('api/user/register', $userData)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = $response->decodeResponseJson();

        $this->assertEquals('The phone format is invalid.', json_decode($responseData->json)->errors->phone[0]);
    }

    public function testLoginUserIfValidCredentials()
    {
        $password = '123123123';
        $user = User::factory()->create(['password' => Hash::make($password)]);

        Auth::login($user);
        $user->refresh();
        Auth::guard('api')->check();

        $this->assertDatabaseHas($this::USER_TABLE, [
            'id' => $user->id,
            'remember_token' => null,
        ]);

        $response = $this->postJson('api/user/login', [
            'email' => $user->email,
            'password' => $password
        ])->assertOk();

        $this->assertDatabaseHas($this::USER_TABLE, [
            'id' => $user->id,
            'remember_token' => null
        ]);
    }

    private function makeUserRegisterData(?array $data = null): array
    {
        $originalUserData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => Str::random(8),
            'phone' => PhoneNumberHelper::generateRandomPhoneNumber(),
            'country_code' => $this->faker->randomElement(['1', '55', '591'])
        ];

        return $data ? array_merge($originalUserData, $data) : $originalUserData;
    }
}
