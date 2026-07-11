<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'wa_number', 'password', 'role', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active'          => 'boolean',
            'password'           => 'hashed',
        ];
    }

    protected function waNumber(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            set: function (?string $value) {
                if (blank($value)) {
                    return null;
                }

                // Hilangkan semua karakter selain angka
                $angka = preg_replace('/\D/', '', $value);

                // Normalisasi ke awalan 62
                if (str_starts_with($angka, '0')) {
                    $angka = '62' . substr($angka, 1);
                } elseif (! str_starts_with($angka, '62')) {
                    $angka = '62' . $angka;
                }

                return $angka;
            },
        );
    }

    // Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && $this->is_active;
    }

    // Role helpers
    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isOperator(): bool  { return $this->role === 'operator'; }
    public function isTeller(): bool    { return $this->role === 'teller'; }
    public function isPelanggan(): bool { return $this->role === 'pelanggan'; }

    // Relasi
    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class);
    }

    public function operatorPelanggan(): HasMany
    {
        return $this->hasMany(OperatorPelanggan::class, 'operator_id');
    }

    // Pelanggan yang ditangani operator ini
    public function pelangganDitangani(): HasMany
    {
        return $this->hasMany(Pelanggan::class, 'user_id');
    }

    public function catatanMeterDicatat(): HasMany
    {
        return $this->hasMany(CatatanMeter::class, 'operator_id');
    }

    public function periodesDibuka(): HasMany
    {
        return $this->hasMany(PeriodePencatatan::class, 'dibuka_oleh');
    }

    public function periodesDitutup(): HasMany
    {
        return $this->hasMany(PeriodePencatatan::class, 'ditutup_oleh');
    }

    public function pembayaranDiproses(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'teller_id');
    }

    public function pembayaranDiverifikasi(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'admin_verifikasi_id');
    }

    public function pengaduanDirespons(): HasMany
    {
        return $this->hasMany(Pengaduan::class, 'admin_id');
    }
}