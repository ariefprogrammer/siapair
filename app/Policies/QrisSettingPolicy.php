<?php

namespace App\Policies;

use App\Models\QrisSetting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QrisSettingPolicy
{
    /**
     * Tentukan siapa saja yang bisa melihat daftar/halaman QRIS Setting.
     */
    public function viewAny(User $user): bool
    {
        // Sesuaikan dengan kebutuhan role Anda, misalnya hanya admin
        return true; 
    }

    /**
     * Tentukan apakah user bisa membuat data QRIS baru.
     */
    public function create(User $user): bool
    {
        // HANYA boleh create jika BELUM ada data sama sekali di database
        return QrisSetting::count() === 0;
    }

    /**
     * Tentukan apakah user bisa mengedit data QRIS.
     */
    public function update(User $user, QrisSetting $qrisSetting): bool
    {
        // Selalu perbolehkan edit jika data sudah ada
        return true; 
    }

    /**
     * Tentukan apakah data boleh dihapus (sebaiknya di-false agar data tunggal tidak hilang).
     */
    public function delete(User $user, QrisSetting $qrisSetting): bool
    {
        return false; 
    }
}