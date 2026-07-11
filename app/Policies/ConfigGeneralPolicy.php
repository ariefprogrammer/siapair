<?php

namespace App\Policies;

use App\Models\ConfigGeneral;
use App\Models\User;

class ConfigGeneralPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Izinkan melihat halaman konfigurasi
    }

    public function create(User $user): bool
    {
        // Kunci tombol Create jika sudah ada baris data pertama hasil migrasi
        return ConfigGeneral::count() === 0;
    }

    public function update(User $user, ConfigGeneral $configGeneral): bool
    {
        return true; // Selalu izinkan edit
    }

    public function delete(User $user, ConfigGeneral $configGeneral): bool
    {
        return false; // Larang hapus agar data tunggal tidak hilang
    }
}