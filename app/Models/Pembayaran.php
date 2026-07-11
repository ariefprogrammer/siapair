<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id', 'metode', 'jumlah_bayar',
        // tunai
        'teller_id', 'nomor_nota',
        // qris
        'bukti_bayar_path', 'status_verifikasi',
        'admin_verifikasi_id', 'catatan_verifikasi', 'tanggal_verifikasi',
        // bersama
        'tanggal_bayar',
    ];

    protected $casts = [
        'jumlah_bayar'       => 'float',
        'tanggal_bayar'      => 'datetime',
        'tanggal_verifikasi' => 'datetime',
    ];

    // Relasi
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function teller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teller_id');
    }

    public function adminVerifikasi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_verifikasi_id');
    }

    // Scope
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'pending');
    }

    public function scopeQris($query)
    {
        return $query->where('metode', 'qris');
    }

    public function scopeTunai($query)
    {
        return $query->where('metode', 'tunai');
    }

    // Helper
    public function isTunai(): bool  { return $this->metode === 'tunai'; }
    public function isQris(): bool   { return $this->metode === 'qris'; }
    public function isPending(): bool    { return $this->status_verifikasi === 'pending'; }
    public function isDisetujui(): bool  { return $this->status_verifikasi === 'disetujui'; }
    public function isDitolak(): bool    { return $this->status_verifikasi === 'ditolak'; }

    public function labelStatusVerifikasi(): string
    {
        return match ($this->status_verifikasi) {
            'pending'   => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => '-',
        };
    }
}