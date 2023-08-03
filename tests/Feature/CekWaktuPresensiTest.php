<?php

namespace Tests\Feature;

use App\Models\Configuration;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CekWaktuPresensiTest extends TestCase
{
    use RefreshDatabase;

    private User $pegawai;

    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pegawai = User::factory()->create();

        $this->seed([
            LocationSeeder::class,
        ]);

        $this->configuration = Configuration::factory()->create();
    }

    public function test_api_cek_waktu_presensi_can_not_be_accessed_by_unauthenticated_user()
    {
        $response = $this->postJson(route('cekWaktuPresensi'), [
            'waktu' => '08:00:00'
        ]);

        $response->assertUnauthorized();
    }

    public function test_hari_libur()
    {
        $this->configuration->hari_libur = Carbon::now()->getTranslatedDayName();

        $this->configuration->save();

        Sanctum::actingAs($this->pegawai);

        $response = $this->postJson(route('cekWaktuPresensi'), [
            'waktu' => '07:15:00'
        ]);

        $response->assertJson([
            'keterangan' => 'Hari libur. Selamat beristirahat bersama orang tersayang.'
        ]);
    }

    public function test_bukan_waktu_presensi()
    {
        Sanctum::actingAs($this->pegawai);

        $response = $this->postJson(route('cekWaktuPresensi'), [
            'waktu' => '10:45:00'
        ]);

        $response->assertJson([
            'keterangan' => 'Bukan waktu presensi.'
        ]);
    }
}
