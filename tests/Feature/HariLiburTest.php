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

class HariLiburTest extends TestCase
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
    }

    public function test_presensi_tidak_dapat_dilakukan_pada_hari_libur()
    {
        $this->configuration->hari_libur = Carbon::now()->getTranslatedDayName();

        $this->configuration->save();

        Sanctum::actingAs($this->pegawai);

        $response = $this->postJson(route('v1.presensi.masuk'));

        $response->assertJson([
            'message' => 'Presensi tidak dapat dilakukan pada hari libur.'
        ]);
    }


}
