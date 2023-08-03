<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\ConfigurationSeeder;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role_id' => Role::isOwner]);

        $this->pegawai = User::factory()->create();

        $this->seed([
            LocationSeeder::class,
            ConfigurationSeeder::class
        ]);
    }

    public function test_configurations_screen_can_not_render_by_unauthenticated_user()
    {
        $response = $this->get(route('configurations.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_pegawai_can_not_render_configurations_page()
    {
        $this->actingAs($this->pegawai);

        $this->followingRedirects()
            ->get(route('configurations.index'))
            ->assertForbidden();
    }

    public function test_owner_can_render_configurations_page()
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('configurations.index'));

        $response->assertOk();
    }
}
