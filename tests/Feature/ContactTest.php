<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', [
            'first_name' => 'Achmad',
            'last_name' => 'Rifqi',
            'email' => 'achmadrifqi09@gmail.com',
            'phone' => '081223183838'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Achmad',
                    'last_name' => 'Rifqi',
                    'email' => 'achmadrifqi09@gmail.com',
                    'phone' => '081223183838',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Rifqi',
            'email' => 'achmad',
            'phone' => '081223183838'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]

                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Rifqi',
            'email' => 'achmad',
            'phone' => '081223183838'
        ], [
            'Authorization' => 'wrong'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'messaage' => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
}
