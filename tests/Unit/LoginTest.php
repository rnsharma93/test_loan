<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function testMustEnterEmailAndPassword()
    {
        $this->json('POST', 'api/user/login')
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    'email' => ["The email field is required."],
                    'password' => ["The password field is required."],
                ]
            ]);
    }

    public function testSuccessfulLogin()
    {
        $user['name'] = "Test Test";
        $user['email'] = 'test@test.com';
        $user['password'] = Hash::make('123456');
        User::create($user);


        $loginData = ['email' => 'test@test.com', 'password' => '123456'];

        $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "token",
                "message"
            ]);

        //$this->assertAuthenticated();
    }

    //test invalid login credentials
    public function testInvalidLogin() {
        $user['name'] = "Test Test";
        $user['email'] = 'test@test.com';
        $user['password'] = Hash::make('123456');
        User::create($user);

        //invalid password
        $loginData = ['email' => 'test@test.com', 'password' => '12345'];

        $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                    "errors" => [
                        "email" => [
                            "The provided credentials are incorrect."
                        ]
                    ]
            ]);
    }

}
