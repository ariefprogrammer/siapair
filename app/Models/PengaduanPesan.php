<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaduanPesan extends Model
{
    protected $table = 'pengaduan_pesan';

    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'pesan',
        'lampiran_path',
    ];

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dariPelanggan(): bool
    {
        return $this->user->role === 'pelanggan';
    }

    public function dariAdmin(): bool
    {
        return in_array($this->user->role, ['admin', 'operator', 'teller']);
    }
}