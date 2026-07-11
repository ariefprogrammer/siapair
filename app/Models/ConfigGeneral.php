<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class ConfigGeneral extends Model
{
    protected $table = 'config_general';

    protected $fillable = [
        'admin_fee',
        'ppn',
        'app_name',
        'app_logo',
        'biaya_beban',
    ];

    protected $casts = [
        'admin_fee' => 'float',
        'ppn' => 'float',
        'biaya_beban' => 'float',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('config_general');
        });
    }
}