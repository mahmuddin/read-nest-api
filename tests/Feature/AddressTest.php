<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->post("/api/contacts/{$contact->id}/addresses", [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '21213',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)
            ->assertJson([
                "data" => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => '21213',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->post("/api/contacts/{$contact->id}/addresses", [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => '',
            'postal_code' => '21213',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'country' => ['The country field is required.'],
                ]
            ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $contact = $contact->id + 1;

        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);

        $response = $this->post("/api/contacts/{$contact}/addresses", [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '21213',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => ['not found']
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->get(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => '21213',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $new_address = $address->id + 1;
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->get(
            "/api/contacts/{$address->contact_id}/addresses/{$new_address}",
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ['not found']
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->put(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => '111111',
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'update',
                    'city' => 'update',
                    'province' => 'update',
                    'country' => 'update',
                    'postal_code' => '111111',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->put(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => '',
                'postal_code' => '111111',
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'country' => ['The country field is required.'],
                ]
            ]);
    }
    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $new_address = $address->id + 1;
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->put(
            "/api/contacts/{$address->contact_id}/addresses/{$new_address}",
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => '111111',
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => ['not found'],
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->delete(
            "/api/contacts/{$address->contact_id}/addresses/{$address->id}",
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $new_address = $address->id + 1;
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->delete(
            "/api/contacts/{$address->contact_id}/addresses/{$new_address}",
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $response->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => ['not found'],
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->get(
            "/api/contacts/{$contact->id}/addresses",
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        'street' => 'test',
                        'city' => 'test',
                        'province' => 'test',
                        'country' => 'test',
                        'postal_code' => '21213',
                    ]
                ]
            ]);
    }

    public function testListContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $contact = $contact->id + 1;
        // Get Token
        $token = JWTAuth::attempt(['username' => 'test', 'password' => 'test']);
        $response = $this->get(
            "/api/contacts/{$contact}/addresses",
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => ['not found'],
                ]
            ]);
    }
}