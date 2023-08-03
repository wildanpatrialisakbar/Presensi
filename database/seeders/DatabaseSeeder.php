<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {

        // Run the Seeder Class
        $this->call([
            RoleSeeder::class,
            LocationSeeder::class,
            ConfigurationSeeder::class,
        ]);

        // Create user with role "owner"
        User::factory()->create([
            'name' => 'Zaka',
            'email' => 'zaka@gmail.com',
            'role_id' => 1,
            'nip' => 'dpm-001',
            'address' => 'Magelang',
            'phone_number' => '6282246876109',
            'birthdate' => '1994-11-01',
            'birthplace' => 'Magelang'
        ]);

        User::factory()->create([
            'name' => 'wildanpat',
            'email' => 'wildanpat@gmail.com',
            'role_id' => 1,
            'nip' => 'dpm-000',
            'address' => 'Magelang',
            'phone_number' => '6282246876109',
            'birthdate' => '2000-1-01',
            'birthplace' => 'Yogyakarta'
        ]);

        // Create user with role employee
        User::factory()->create([
            'name' => 'Ridwan Arifin',
            'email' => 'Ridwan@gmail.com',
            'nip' => 'dpm-002',
            'address' => 'Sleman',
            'phone_number' => '6281315701116',
            'birthplace' => 'Sleman',
            'birthdate' => '1994-01-04',
        ]);

        User::factory()->create([
            'name' => 'Dandi Pratama',
            'email' => 'Dandi@gmail.com',
            'nip' => 'dpm-003',
            'address' => 'Yogyakarta',
            'phone_number' => '6286753231097',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1990-04-28',
        ]);

        User::factory()->create([
            'name' => 'Afif Sulhan',
            'email' => 'afif@gmail.com',
            'nip' => 'dpm-004',
            'address' => 'Solo',
            'phone_number' => '6285329503198',
            'birthplace' => 'Solo',
            'birthdate' => '1997-04-15',
        ]);

        User::factory()->create([
            'name' => 'Panji Sangaji',
            'email' => 'panji@gmail.com',
            'nip' => 'dpm-005',
            'address' => 'Yogyakarta',
            'phone_number' => '6282888453278',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1992-07-13',
        ]);

        User::factory()->create([
            'name' => 'Aji Kurniawan',
            'email' => 'aji@gmail.com',
            'nip' => 'dpm-006',
            'address' => 'Sleman',
            'phone_number' => '6285321786543',
            'birthplace' => 'Sleman',
            'birthdate' => '1992-06-28',
        ]);

        User::factory()->create([
            'name' => 'Hanifa Azka',
            'email' => 'hanifa@gmail.com',
            'nip' => 'dpm-007',
            'address' => 'Ngluwar',
            'phone_number' => '6287734786439',
            'birthplace' => 'Magelang',
            'birthdate' => '1995-05-20',
        ]);

        User::factory()->create([
            'name' => 'Tri Adek Prasetyo',
            'email' => 'tri@gmail.com',
            'nip' => 'dpm-008',
            'address' => 'Sleman',
            'phone_number' => '6282328283355',
            'birthplace' => 'Sleman',
            'birthdate' => '1992-08-22',
        ]);

        User::factory()->create([
            'name' => 'Syaifudin',
            'email' => 'syaifudin@gmail.com',
            'nip' => 'dpm-009',
            'address' => 'Sleman',
            'phone_number' => '6282217949513',
            'birthplace' => 'Sleman',
            'birthdate' => '1995-05-29',
        ]);

        User::factory()->create([
            'name' => 'Atika Loreanti',
            'email' => 'atika@gmail.com',
            'nip' => 'dpm-010',
            'address' => 'Yogyakarta',
            'phone_number' => '6281378443339',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1994-04-25',
        ]);

        User::factory()->create([
            'name' => 'Ardo Hutoma',
            'email' => 'ardo@gmail.com',
            'nip' => 'dpm-011',
            'address' => 'Magelang',
            'phone_number' => '6281245855253',
            'birthplace' => 'Magelang',
            'birthdate' => '1994-09-30',
        ]);

        User::factory()->create([
            'name' => 'Ahmat Riyan',
            'email' => 'ahmat@gmail.com',
            'nip' => 'dpm-012',
            'address' => 'Sleman',
            'phone_number' => '6282251002406',
            'birthplace' => 'Sleman',
            'birthdate' => '1995-02-17',
        ]);

        User::factory()->create([
            'name' => 'Rida Tustinaningsih',
            'email' => 'Rida@gmail.com',
            'nip' => 'dpm-013',
            'address' => 'Sleman',
            'phone_number' => '6285882815866',
            'birthplace' => 'Sleman',
            'birthdate' => '1997-02-02',
        ]);

        User::factory()->create([
            'name' => 'Rahmat Yulianto',
            'email' => 'Rahmat@gmail.com',
            'nip' => 'dpm-014',
            'address' => 'Bantul',
            'phone_number' => '6282346762029',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1997-01-23',
        ]);

        User::factory()->create([
            'name' => 'Sigit Wahyudi',
            'email' => 'sigit@gmail.com',
            'nip' => 'dpm-015',
            'address' => 'Yogyakarta',
            'phone_number' => '6281674358971',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1990-10-27',
        ]);

        User::factory()->create([
            'name' => 'Sujiyo',
            'email' => 'sujiyo@gmail.com',
            'password' => bcrypt('password1'),
            'nip' => 'dpm-016',
            'address' => 'Yogyakarta',
            'phone_number' => '6285527367995',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1993-10-13',
        ]);

        User::factory()->create([
            'name' => 'Putra Jurid',
            'email' => 'putrajurid@gmail.com',
            'nip' => 'dpm-017',
            'address' => 'Solo',
            'phone_number' => '6285083456785',
            'birthplace' => 'Solo',
            'birthdate' => '1992-01-05',
        ]);

        User::factory()->create([
            'name' => 'Muhammad Rasyid',
            'email' => 'muhammad@gmail.com',
            'nip' => 'dpm-018',
            'address' => 'Magelang',
            'phone_number' => '6289342756441',
            'birthplace' => 'Magelang',
            'birthdate' => '1992-11-10',
        ]);

        User::factory()->create([
            'name' => 'Nada Yusfarida',
            'email' => 'nada@gmail.com',
            'nip' => 'dpm-019',
            'address' => 'Yogyakarta',
            'phone_number' => '6287642213087',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1993-08-18',
        ]);

        User::factory()->create([
            'name' => 'Ngadino',
            'email' => 'ngadino@gmail.com',
            'nip' => 'dpm-020',
            'address' => 'Yogyakarta',
            'phone_number' => '6287845113789',
            'birthplace' => 'Yogyakarta',
            'birthdate' => '1985-05-20',
        ]);
    }
}
