<?php

namespace Database\Seeders;

use App\Models\Scenes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScenesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scenes = [
            [
                'scene_number' => '01',
                'location' => 'INT. RUANG BENGKEL - DAY',
                'description' => 'Ghani, Arif, Al-Kahfi, V.O Indah',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '02',
                'location' => 'INT. RUANG BENGKEL - LATER',
                'description' => 'Dosen, Fahmi, Geva, Argha, Fawaz',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '03',
                'location' => 'INT. RUANG PRAKTIKUM - SIANG',
                'description' => 'Ghani, Fahmi, Fawaz, Dimaz, Al-Kahfi, Arif',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '04',
                'location' => 'INT. RUANG PRAKTIKUM - BEBERAPA SAAT KEMUDIAN',
                'description' => 'Fawaz, Geva, Dimaz, Al-Kahfi, V.O. Indah, Ghani',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '05',
                'location' => 'INT. RUANG PRAKTIKUM - BEBERAPA MENIT KEMUDIAN',
                'description' => 'Fawaz, Fahmi, Arif, Alvi, Ghani, V.O. Indah',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '06',
                'location' => 'INT. RUANG PRAKTIKUM - SEBELUM KELUAR',
                'description' => 'Ghani, Dimaz, Fahmi',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '07',
                'location' => 'INT. KONTRAKAN EC-D – SORE',
                'description' => 'Fahmi, Dimaz, Geva, Ghani, Arif',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '08',
                'location' => 'INT. KONTRAKAN EC-D – HARI BERIKUTNYA',
                'description' => 'Geva, Dhea, Fadly, Bintang, Fahmi, Ghani, Al-Kahfi, Arif, Indah',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '09',
                'location' => 'INT. KONTRAKAN EC-D – BEBERAPA HARI KEMUDIAN',
                'description' => 'Ghani, Dhea, Fadly, Indah, Bintang, Geva, Fahmi',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '10',
                'location' => 'INT. KELAS TEKNIK ELEKTRO – PAGI BERIKUTNYA',
                'description' => 'Ghani, Arif, Bintang, Al-Kahfi, V.O. Indah',
                'shoot_date' => '2025-05-28'
            ],
            [
                'scene_number' => '11',
                'location' => 'INT. KELAS TEKNIK ELEKTRO – SORE HARI – PULANG KULIAH',
                'description' => 'Ghani, Dimaz, V.O. Indah',
                'shoot_date' => '2025-05-28'
            ],
        ];

        foreach ($scenes as $scene) {
            Scenes::create($scene);
        }
    }
}
