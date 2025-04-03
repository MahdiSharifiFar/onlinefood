<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    protected $table = 'favorites';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id');
    }
}
