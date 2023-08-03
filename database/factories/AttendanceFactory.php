<?php

namespace Database\Factories;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'latitude_masuk' => fake()->latitude,
            'latitude_pulang' => fake()->latitude,
            'longitude_masuk' => fake()->longitude,
            'longitude_pulang' => fake()->longitude,
            'distance_masuk' => rand(10, 100),
            'distance_pulang' => rand(10, 100),
            'jam_masuk' => Carbon::create('07:30:00')->toDateTimeString(),
            'jam_pulang' => Carbon::create('16:30:00')->toDateTimeString(),
            'lembur' => rand(1,6)
        ];
    }
}
