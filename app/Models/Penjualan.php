<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [
        'created_at',
        'deleted_at'
    ];

    public function detail() {
        return $this->hasMany(DetailPenjualan::class, 'kodePenjualan', 'kodePenjualan');
    }

    public function pelanggan() {
        return $this->belongsTo(Pelanggan::class);
    }
}
