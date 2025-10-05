<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'Jon',
            'last_name' => 'Snow',
            'email' => 'jon@nightwatch.com',
            'phone' => '0987261721812',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Jon',
                    'last_name' => 'Snow',
                    'email' => 'jon@nightwatch.com',
                    'phone' => '0987261721812',
                ]
            ]);
    }

    public function testCreateFailed(): void
    {
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Snow',
            'email' => 'jon',
            'phone' => '0987261721812',
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
                    ],
                ]
            ]);
    }

    public function testCreateUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Snow',
            'email' => 'jon',
            'phone' => '0987261721812',
        ], [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ],
                ]
            ]);
    }

    public function testGetSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/{$contact->id}", [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@local.com',
                    'phone' => '12345',
                ]
            ]);
    }

    public function testGetNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ],
                ]
            ]);
    }

    public function testGetOtherUserContacts(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . ($contact->id), [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ],
                ]
            ]);
    }

    public function testupdateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/{$contact->id}", [
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test2@local.com',
            'phone' => '1234522',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test2',
                    'last_name' => 'test2',
                    'email' => 'test2@local.com',
                    'phone' => '1234522',
                ]
            ]);
    }

    public function testupdateValidationError(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/{$contact->id}", [
            'first_name' => '',
            'last_name' => 'test2',
            'email' => 'test2@local.com',
            'phone' => '1234522',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                ]
            ]);
    }

    public function testDeleteSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete("/api/contacts/{$contact->id}", [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete("/api/contacts/" . ($contact->id + 1), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testSearchByFirstName(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $res = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($res, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($res['data']));
        self::assertEquals(20, $res['meta']['total']);
    }

    public function testSearchByLastName(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $res = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($res, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($res['data']));
        self::assertEquals(20, $res['meta']['total']);
    }

    public function testSearchByEmail(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $res = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($res, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($res['data']));
        self::assertEquals(20, $res['meta']['total']);
    }

    public function testSearchByPhone(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $res = $this->get('/api/contacts?phone=11111', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($res, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($res['data']));
        self::assertEquals(20, $res['meta']['total']);
    }

    public function testSearchNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $res = $this->get('/api/contacts?name=tidakada', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($res, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($res['data']));
        self::assertEquals(0, $res['meta']['total']);
    }

    public function testSearchWithPage(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $res = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($res, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($res['data']));
        self::assertEquals(20, $res['meta']['total']);
        self::assertEquals(2, $res['meta']['current_page']);
    }
}
