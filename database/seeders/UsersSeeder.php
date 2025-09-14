<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Fahmi', 'email' => 'fahmi@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Arif', 'email' => 'arif@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Fadly', 'email' => 'fadly@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Bintang', 'email' => 'bintang@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Ghani', 'email' => 'ghani@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            // tambah sisanya 6 orang lagi
            ['name' => 'Indah', 'email' => 'indah@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Geva', 'email' => 'geva@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Dimaz', 'email' => 'dimaz@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'AlKahfi', 'email' => 'alkahfi@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Alvi', 'email' => 'alvi@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Fawaz', 'email' => 'fawaz@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
            ['name' => 'Dhea', 'email' => 'dhea@shortfilm.com', 'password' => Hash::make('qweqweasd'), 'active' => '1'],
        ];

        foreach ($users as $index => $userData) {
            $user = User::create($userData);

            // Assign Role
            switch ($user->name) {
                case 'Fahmi':
                case 'Ghani':
                    $user->addRole('admin');
                    break;
                case 'Arif':
                    $user->addRole('editor');
                    break;
                case 'Fadly':
                case 'Bintang':
                    $user->addRole('cameramen');
                    break;
                default:
                    $user->addRole('user');
                    break;
            }
        }
    }
}
