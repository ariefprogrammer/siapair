<?php

namespace App\Policies;

use App\Models\Tagihan;
use App\Models\User;

class TagihanPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Mengizinkan melihat tabel daftar tagihan
    }

    public function view(User $user, Tagihan $tagihan): bool
    {
        return true; // Mengizinkan melihat detail tagihan
    }

    public function create(User $user): bool
    {
        return false; // KUNCI / NONAKTIFKAN pembuatan tagihan baru dari panel admin
    }

    public function update(User $user, Tagihan $tagihan): bool
    {
        return true; // Mengizinkan ubah status / data tagihan
    }

    public function delete(User $user, Tagihan $tagihan): bool
    {
        return true; // Mengizinkan hapus tagihan jika diperlukan
    }
}