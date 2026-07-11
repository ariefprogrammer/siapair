<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodePencatatan extends Model
{
    protected $table = 'periode_pencatatan';

    protected $fillable = [
        'bulan', 'tahun', 'status',
        'dibuka_oleh', 'ditutup_oleh',
        'dibuka_at', 'ditutup_at',
    ];

    protected $casts = [
        'dibuka_at'  => 'datetime',
        'ditutup_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $periode) {
            if ($periode->status === 'buka') {
                static::where('status', 'buka')
                    ->where('id', '!=', $periode->id ?? 0)
                    ->update(['status' => 'tutup']);
            }
        });
    }

    // Relasi
    public function dibuka(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    public function ditutup(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditutup_oleh');
    }

    public function catatanMeter(): HasMany
    {
        return $this->hasMany(CatatanMeter::class, 'periode_id');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'periode_id');
    }

    // Scope
    public function scopeBuka($query)
    {
        return $query->where('status', 'buka');
    }

    // Helper
    public function isBuka(): bool
    {
        return $this->status === 'buka';
    }

    public function labelBulan(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($bulan[$this->bulan] ?? '?') . ' ' . $this->tahun;
    }
}