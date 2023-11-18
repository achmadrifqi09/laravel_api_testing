<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'username' => 'rifqi09',
            'name' => 'rifqi',
            'password' => 'rahasia'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'rifqi09',
                    'name' => 'rifqi',
                ]
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'name' => '',
            'password' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],

                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists(): void
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'rifqi09',
            'name' => 'rifqi',
            'password' => 'rahasia'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'username already registered'
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            'username' => 'achmadrifqi09',
            'password' => 'rahasia',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'achmadrifqi09',
                    'name' => 'Achmad Rifqi',
                ]
            ]);

        $user = User::where('username', 'achmadrifqi09')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailed()
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password wrong'
                    ],
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'achmadrifqi09',
                    'name' => 'Achmad Rifqi'
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'messaage' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'wrong'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'messaage' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }


    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'achmadrifqi09')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'rifqi'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'achmadrifqi09',
                    'name' => 'rifqi'
                ]
            ]);

        $newUser = User::where('username', 'achmadrifqi09')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }
    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'achmadrifqi09')->first();

        $this->patch(
            '/api/users/current',
            [
                'password' => 'new password'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'achmadrifqi09',
                    'name' => 'Achmad Rifqi'
                ]
            ]);

        $newUser = User::where('username', 'achmadrifqi09')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }
    public function testUpdateNameFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. '
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }
    public function testUpdatePasswordFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            '/api/users/current',
            [
                'password' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. '
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testLogoutSucces()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'true'
                ]
            ]);
        $user = User::where('username', 'achmadrifqi09')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'wrong'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'messaage' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
