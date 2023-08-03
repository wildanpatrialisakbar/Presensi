<?php

namespace Tests\Feature;

use App\Models\Configuration;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $employee;

    protected function setUp():void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role_id' => Role::isOwner]);
        $this->employee = User::factory()->create();

        $this->seed([
            LocationSeeder::class
        ]);

        Configuration::factory()->create();
    }


    /**
     * Test open attendance page by employee
     *
     * @return void
     */
    public function test_attendances_screen_cannot_rendered_by_employee(): void
    {
        $this->actingAs($this->employee);

        $this->followingRedirects()
            ->get(route('attendances.index'))
            ->assertForbidden();
    }

    /**
     * Test open attendance create page by employee
     *
     * @return void
     */
    public function test_attendances_create_screen_cannot_rendered_by_employee(): void
    {
        $this->actingAs($this->employee);

        $this->followingRedirects()
            ->get(route('attendances.create'))
            ->assertForbidden();
    }

    /**
     * Test open attendance page by owner
     *
     * @return void
     */
    public function test_attendances_screen_can_rendered_by_owner(): void
    {
        $this->actingAs($this->owner);

        $this->followingRedirects()
            ->get(route('attendances.index'))
            ->assertOk();
    }

    /**
     * Test open attendance create page by owner
     *
     * @return void
     */
    public function test_attendances_create_screen_can_rendered_by_owner(): void
    {
        $this->actingAs($this->owner);

        $this->followingRedirects()
            ->get(route('attendances.create'))
            ->assertOk();
    }

    /**
     * Test store attendances
     *
     * @return void
     */
    public function test_store_attendances(): void
    {
        $this->actingAs($this->owner);

        $this->followingRedirects()
            ->post(route('attendances.store'), [
                'status' => 'Hadir',
                'user_id' => $this->employee->id,
                'tanggal' => now()->toDateString()
            ])
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->where('errors', [])
                ->component('Attendance/Index')
                ->where('flash.message', 'Presensi berhasil disimpan.')
            );
    }
}
