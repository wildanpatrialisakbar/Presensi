<?php

namespace Database\Factories;

use App\Models\Configuration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Configuration>
 */
class ConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => 3,
            'accepted_distance' => 150,
            'presensi_masuk_dari' => '07:00:00',
            'presensi_masuk_sampai' => '07:30:00',
            'toleransi_keterlambatan' => '07:45:00',
            'maksimal_terlambat' => '08:00:00',
            'presensi_pulang_dari' => '16:00',
            'presensi_pulang_sampai' => '17:00',
            'hari_libur' => 'Jumat'
        ];
    }
}
