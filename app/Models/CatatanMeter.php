<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CatatanMeter extends Model
{
    protected $table = 'catatan_meter';

    protected $fillable = [
        'pelanggan_id', 'operator_id', 'periode_id',
        'angka_meter_lalu', 'angka_meter_sekarang',
        'status_kondisi', 'catatan', 'foto_path', 'dicatat_at',
    ];

    protected $casts = [
        'angka_meter_lalu'     => 'float',
        'angka_meter_sekarang' => 'float',
        'pemakaian'            => 'float',
        'dicatat_at'           => 'datetime',
    ];

    // Relasi
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePencatatan::class, 'periode_id');
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class);
    }

    // Scope
    public function scopeByPeriode($query, int $periodeId)
    {
        return $query->where('periode_id', $periodeId);
    }

    public function scopeAnomalik($query)
    {
        return $query->where('status_kondisi', '!=', 'normal');
    }

    // Helper — fallback jika VIRTUAL column tidak terbaca ORM
    public function getPemakaianAttribute($value): float
    {
        return $value ?? ($this->angka_meter_sekarang - $this->angka_meter_lalu);
    }

    public function sudahAdaTagihan(): bool
    {
        return $this->tagihan()->exists();
    }
}