<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $fillable = [
        'pelanggan_id', 'kategori', 'deskripsi',
        'lampiran_path', 'status',
        'respons_admin', 'admin_id', 'tanggal_respons',
    ];

    protected $casts = [
        'tanggal_respons' => 'datetime',
    ];

    // Relasi
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scope
    public function scopeMasuk($query)    { return $query->where('status', 'masuk'); }
    public function scopeDiproses($query) { return $query->where('status', 'diproses'); }
    public function scopeSelesai($query)  { return $query->where('status', 'selesai'); }

    // Helper
    public function labelStatus(): string
    {
        return match ($this->status) {
            'masuk'    => 'Masuk',
            'diproses' => 'Diproses',
            'selesai'  => 'Selesai',
            default    => '-',
        };
    }

    public function labelKategori(): string
    {
        return match ($this->kategori) {
            'teknis'        => 'Teknis',
            'administrasi'  => 'Administrasi',
            'lainnya'       => 'Lainnya',
            default         => '-',
        };
    }

    public function pesan(): HasMany
    {
        return $this->hasMany(PengaduanPesan::class)->oldest();
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function pesanTerakhir(): ?PengaduanPesan
    {
        return $this->pesan()->latest()->first();
    }
}