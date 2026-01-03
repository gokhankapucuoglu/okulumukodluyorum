<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::firstOrCreate(
            ['institution_code' => '762764'],
            [
                'province' => 'Şanlıurfa',
                'district' => 'Karaköprü',
                'name' => 'Milli İrade Kız Anadolu İmam Hatip Lisesi',
                'type' => 'Devlet',
                'level' => 'Lise',
                'program' => 'Fen ve Sosyal Bilimler Proje Okulu',
                'education_time' => 'Tam Gün',
                'start_time' => '08:30:00',
                'end_time' => '15:30:00',
                'address' => 'Akpiyar Mh. 403 Cad. No:1A',
                'phone' => '0414 347 10 29',
                'email' => '762764@meb.k12.tr',
                'is_active' => true,
            ]
        );
    }
}
