<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetUserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_api_user_profile_can_not_accessed_by_unauthenticated_user()
    {
        $response = $this->getJson(route('v1.auth.userProfile'));

        $response->assertUnauthorized();
    }

    public function test_get_user_profile()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('v1.auth.userProfile'));

        $response->assertOk();
    }


}
