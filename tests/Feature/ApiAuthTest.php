<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }


    public function test_login()
    {
        $response = $this->postJson(route('v1.auth.login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'device_name' => 'Samsung S22 Ultra'
        ]);

        $response->assertJson([
            "message" => "Login success"
        ]);
    }

    public function test_login_with_invalid_password()
    {
        $response = $this->postJson(route('v1.auth.login', [
            'email' => $this->user->email,
            'password' => 'wrong password',
            'device_name' => 'Samsung S22 Ultra'
        ]));

        $response->assertUnprocessable();
    }

    public function test_get_user_profile()
    {
        Sanctum::actingAs($this->user);

        $response = $this->get(route('v1.auth.userProfile'));

        $response->assertOk();
    }

    public function test_logout()
    {
        Sanctum::actingAs($this->user);

        $response = $this->post(route('v1.auth.logout'));

        $response->assertOk();
    }
}
