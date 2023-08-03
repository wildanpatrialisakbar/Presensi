<?php

namespace Tests\Feature;

use App\Models\OffWork;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ConfigurationSeeder;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class OffWorkTest extends TestCase
{
    use RefreshDatabase;

    private User $asEmployee;
    private User $asOwner;

    private OffWork $offWork;

    protected function setUp():void
    {
        parent::setUp();

        $this->asEmployee = User::factory()->create(['role_id' => Role::isPegawai]);
        $this->asOwner = User::factory()->create(['role_id' => Role::isOwner]);
        $this->offWork = OffWork::factory()->for($this->asEmployee)->create();

        $this->seed([
            LocationSeeder::class,
            ConfigurationSeeder::class
        ]);
    }


    public function test_off_work_screen_can_be_rendered()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->get(route('offworks.index'))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia->component('Offwork/Index'));
    }

    public function test_off_work_create_screen_can_be_rendered()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->get(route('offworks.create'))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia->component('Offwork/Create'));
    }

    public function test_insert_new_off_work()
    {
        $this->actingAs($this->asEmployee);

        Storage::fake('local');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->followingRedirects()
            ->post(route('offworks.store'), [
                'start_date' => now()->addDay()->toDateTimeString(),
                'finish_date' => now()->addDays(3)->toDateTimeString(),
                'reason' => "I'm sick",
                'document' => $file
            ])
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $assertableInertia) => $assertableInertia
                    ->where('errors', [])
                    ->component('Offwork/Index')
                    ->where('flash.message', 'Permohonan cuti berhasil dibuat.')
            );

        Storage::disk('local')->assertExists('public/offworks/' . $file->hashName());
    }

    public function test_show_off_work()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->get(route('offworks.show', $this->offWork->id))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia->component('Offwork/Show'));
    }

    public function test_form_edit_off_work_can_be_rendered()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->get(route('offworks.edit', $this->offWork->id))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia->component('Offwork/Edit'));
    }

    public function test_update_off_work()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->put(route('offworks.update', $this->offWork->id), [
                'start_date' => '2022-11-12',
                'finish_date' => '2022-11-15',
                'reason' => "I'm sick update",
            ])
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia
                ->where('errors', [])
                ->where('flash.message', 'Permohonan cuti berhasil diperbarui.')
                ->component('Offwork/Index')
            );

        $this->assertDatabaseHas('off_works', [
            'user_id' => $this->offWork->user_id,
            'start_date' => '2022-11-12',
            'finish_date' => '2022-11-15',
            'reason' => "I'm sick update",
        ]);
    }

    public function test_delete_off_work()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->delete(route('offworks.destroy', $this->offWork->id))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia
                ->where('errors', [])
                ->where('flash.message', 'Permohonan cuti berhasil dihapus.')
                ->component('Offwork/Index')
            );

        $this->assertDatabaseMissing('off_works', [
            'id' => $this->offWork->id
        ]);
    }

    public function test_status_off_work_can_not_be_random()
    {
        $this->actingAs($this->asOwner);

        $this->followingRedirects()
            ->put(route('offworks.updateStatus', $this->offWork->id), [
                'status' => 'random_status'
            ])
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia
                ->where('errors.status', 'The selected status is invalid.')
            );

        $this->assertDatabaseHas('off_works', [
            'id' => $this->offWork->id,
            'status' => 'menunggu'
        ]);
    }

    public function test_update_status_off_work()
    {
        $this->actingAs($this->asOwner);

        $this->followingRedirects()
            ->put(route('offworks.updateStatus', $this->offWork->id), [
                'status' => 'disetujui'
            ])
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $assertableInertia) => $assertableInertia
                ->where('errors', [])
            );

        $this->assertDatabaseHas('off_works', [
            'id' => $this->offWork->id,
            'status' => 'disetujui'
        ]);
    }

    public function test_pegawai_can_not_update_the_status()
    {
        $this->actingAs($this->asEmployee);

        $this->followingRedirects()
            ->put(route('offworks.updateStatus', $this->offWork->id), [
                'status' => 'disetujui'
            ])
            ->assertForbidden();
    }


}
