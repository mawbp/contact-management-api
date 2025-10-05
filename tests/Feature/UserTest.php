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
            'username' => 'jon',
            'password' => '12345',
            'name' => 'Jon Snow',
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'jon',
                    'name' => 'Jon Snow'
                ]
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExist(): void
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'jon',
            'password' => '12345',
            'name' => 'Jon Snow',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'Username already registered'
                    ],
                ]
            ]);
    }

    public function testLoginSuccess(): void
    {
        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ]
            ]);
        
        $user = \App\Models\User::query()->where('username', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound(): void
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Username or password wrong'
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong(): void
    {
        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'salah',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Username or password wrong'
                    ]
                ]
            ]);
    }

    public function testGetSuccess(): void
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);
    }

    public function testGetUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $olduser = User::query()->where('username', 'test')->first();

        $this->patch('/api/users/current', [
            'name' => 'jon'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'jon'
                ]
            ]);

        $newuser = User::query()->where('username', 'test')->first();
        self::assertNotEquals($olduser->name, $newuser->name);
    }

    public function testUpdatePasswordSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $olduser = User::query()->where('username', 'test')->first();

        $this->patch('/api/users/current', [
            'password' => 'baru'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);

        $newuser = User::query()->where('username', 'test')->first();
        self::assertNotEquals($olduser->password, $newuser->password);
    }

    public function testUpdateFailed(): void
    {
        $this->seed([UserSeeder::class]);
        $olduser = User::query()->where('username', 'test')->first();

        $this->patch('/api/users/current', [
            'name' => 'abcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcdeabcde'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->delete('/api/users/logout', [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
        ]);

        $user = User::query()->where('username', 'test')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed(): void
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: 'api/users/logout', headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
        ]);
    }
}
