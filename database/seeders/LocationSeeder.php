<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::create([
            'name' => 'UTY Campus 1',
            'latitude' => '-7.747424881612259',
            'longitude' => '110.3554123613803',
        ]);

        Location::create([
            'name' => 'Kosan',
            'latitude' => '-7.7567961212944',
            'longitude' => '110.35635869236',
        ]);

        Location::create([
            'name' => 'PT.Arsipada Karya Utama',
            'latitude' => '-7.770184944447205',
            'longitude' => '110.36181592276499',
            'status' => 'active'
        ]);
    }
}
