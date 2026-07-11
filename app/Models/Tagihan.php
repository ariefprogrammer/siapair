<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'pelanggan_id', 'catatan_meter_id', 'periode_id',
        'total_pemakaian', 'breakdown_tarif',
        'biaya_air', 'biaya_admin', 'biaya_beban', 'total_tagihan',
        'status', 'tanggal_jatuh_tempo', 'diubah_oleh',
    ];

    protected $casts = [
        'breakdown_tarif'     => 'array',
        'total_pemakaian'     => 'float',
        'biaya_air'           => 'float',
        'biaya_admin'         => 'float',
        'biaya_beban'         => 'float',
        'total_tagihan'       => 'float',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // Relasi
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function catatanMeter(): BelongsTo
    {
        return $this->belongsTo(CatatanMeter::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePencatatan::class, 'periode_id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Scope
    public function scopeBelumDibayar($query)
    {
        return $query->where('status', 'belum_dibayar');
    }

    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'menunggu_verifikasi');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    // Helper
    public function isBelumDibayar(): bool        { return $this->status === 'belum_dibayar'; }
    public function isMenungguVerifikasi(): bool   { return $this->status === 'menunggu_verifikasi'; }
    public function isLunas(): bool                { return $this->status === 'lunas'; }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'belum_dibayar'       => 'Belum Dibayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'lunas'               => 'Lunas',
            default               => '-',
        };
    }
}