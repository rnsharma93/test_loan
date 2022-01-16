<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', 'api/user/create', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testSuccessfulRegistration()
    {
        $userData = [
            "name" => "Test Test",
            "email" => "test@gmail.com",
            "password" => "123456"
        ];

        $this->json('POST', 'api/user/create', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "token",
                "message"
            ]);
    }

    public function testEmailAlreadyExist() {
        $userData = [
            "name" => "Test Test",
            "email" => "test@gmail.com",
            "password" => "123456"
        ];

        User::create($userData);

        $this->json('POST', 'api/user/create', $userData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                    "message" => "The given data was invalid.",
                    "errors" => [
                        "email" => [
                            "The email has already been taken."
                        ]
                    ]
            ]);
    }
}
