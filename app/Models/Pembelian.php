<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [
        'created_at',
        'deleted_at'
    ];

    public function detail() {
        return $this->hasMany(DetailPembelian::class);
    }

    public function produk()
    {
        return $this->hasManyThrough(Produk::class, DetailPembelian::class);
    }
}
