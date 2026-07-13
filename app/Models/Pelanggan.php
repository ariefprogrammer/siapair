<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id', 'nomor_sambungan', 'nama', 'alamat',
        'rt', 'rw', 'wilayah',
        'latitude', 'longitude',
        'status', 'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Pelanggan $pelanggan) {
            // Hapus akun user terkait (jika ada) saat pelanggan dihapus
            $pelanggan->user()?->delete();
        });
    }

    // Operator yang menangani pelanggan ini (via pivot)
    public function operators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'operator_pelanggan', 'pelanggan_id', 'operator_id')
                    ->withPivot('assigned_at')
                    ->orderByPivot('assigned_at', 'desc');
    }

    // Operator aktif (assignment terbaru)
    public function operatorAktif(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); // override jika ada kolom khusus
    }

    public function catatanMeter(): HasMany
    {
        return $this->hasMany(CatatanMeter::class);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pengaduan(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }

    public function operatorPelanggan(): HasMany
    {
        return $this->hasMany(OperatorPelanggan::class);
    }

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByWilayah($query, string $wilayah)
    {
        return $query->where('wilayah', $wilayah);
    }

    // Helper
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}