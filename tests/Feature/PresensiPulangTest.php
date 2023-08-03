<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Configuration;
use App\Models\User;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PresensiPulangTest extends TestCase
{
    use RefreshDatabase;

    private User $pegawai;

    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pegawai = User::factory()->create();

        $this->seed([LocationSeeder::class]);

        $this->configuration = Configuration::factory()->create();

        Attendance::factory()->for($this->pegawai)->create();
    }

    public function test_api_presensi_pulang_can_not_accessed_by_unauthenticated_user()
    {
        $response = $this->postJson(route('v1.presensi.pulang'));

        $response->assertUnauthorized();
    }

    public function test_presensi_pulang()
    {
        Sanctum::actingAs($this->pegawai);

        $response = $this->postJson(route('v1.presensi.pulang'), [
            'latitude_pulang' => $this->configuration->location->latitude,
            'longitude_pulang' => $this->configuration->location->longitude,
            'waktu' => '16:15:00'
        ]);

        $response->assertOk();
    }

    public function test_jika_jarak_terlalu_jauh_dari_titik_lokasi_presensi_maka_tidak_bisa_presensi()
    {
        Sanctum::actingAs($this->pegawai);

        $response = $this->postJson(route('v1.presensi.pulang'), [
            'latitude_pulang' => 1,
            'longitude_pulang' => 2,
            'waktu' => '16:15:00'
        ]);

        $response->assertJson([
            'message' => 'Presensi gagal karena jarak Anda terlalu jauh dari titik presensi.'
        ]);
    }

    public function test_tidak_bisa_presensi_pulang_karena_bukan_waktu_presensi_pulang()
    {
        Sanctum::actingAs($this->pegawai);

        $response = $this->postJson(route('v1.presensi.pulang'), [
            'latitude_pulang' => 1,
            'longitude_pulang' => 2,
            'waktu' => '17:15:00'
        ]);

        $response->assertUnprocessable();
    }
}
