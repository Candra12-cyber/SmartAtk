<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'nip' => '198001012000121001',
            'username' => 'admin_pn',
            'email' => 'admin@pn-garut.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'jabatan' => 'Administrator Sistem',
            'unit_kerja' => 'Bagian Umum',
            'telepon' => '+62 811 1111 1111',
        ]);

        User::create([
            'name' => 'Budi Santoso, S.H., M.H.',
            'nip' => '198012312005011002',
            'username' => 'budi.santoso_pn',
            'email' => 'budi.s@pn-garut.go.id',
            'password' => Hash::make('password'),
            'role' => 'user',
            'jabatan' => 'Panitera Pengganti',
            'unit_kerja' => 'Kepaniteraan Perdata',
            'telepon' => '+62 812 3456 7890',
        ]);
    }
}