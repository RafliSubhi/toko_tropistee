<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengunjung_id',
        'product_id',
        'quantity',
    ];

    public function pengunjung()
    {
        return $this->belongsTo(Pengunjung::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
