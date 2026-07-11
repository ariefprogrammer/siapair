<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@siapair.local',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Operator
        $operator = User::create([
            'name'      => 'Operator Satu',
            'email'     => 'operator@siapair.local',
            'password'  => Hash::make('password'),
            'role'      => 'operator',
            'is_active' => true,
        ]);

        // Teller
        User::create([
            'name'      => 'Teller Satu',
            'email'     => 'teller@siapair.local',
            'password'  => Hash::make('password'),
            'role'      => 'teller',
            'is_active' => true,
        ]);

        // Pelanggan — buat user + data pelanggan sekaligus
        $userPelanggan = User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'budi@siapair.local',
            'password'  => Hash::make('password'),
            'role'      => 'pelanggan',
            'is_active' => true,
        ]);

        Pelanggan::create([
            'user_id'          => $userPelanggan->id,
            'nomor_sambungan'  => 'SA-0001',
            'nama'             => 'Budi Santoso',
            'alamat'           => 'Jl. Merdeka No. 1',
            'rt'               => '001',
            'rw'               => '002',
            'wilayah'          => 'Blok A',
            'status'           => 'aktif',
            'tanggal_daftar'   => '2024-01-01',
        ]);

        // Pelanggan kedua — tanpa akun login
        Pelanggan::create([
            'user_id'          => null,
            'nomor_sambungan'  => 'SA-0002',
            'nama'             => 'Siti Rahayu',
            'alamat'           => 'Jl. Mawar No. 5',
            'rt'               => '001',
            'rw'               => '002',
            'wilayah'          => 'Blok A',
            'status'           => 'aktif',
            'tanggal_daftar'   => '2024-01-01',
        ]);

        // Mapping operator ke pelanggan
        $operator->operatorPelanggan()->createMany([
            ['pelanggan_id' => 1, 'assigned_at' => now()],
            ['pelanggan_id' => 2, 'assigned_at' => now()],
        ]);
    }
}