<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;
    protected $table = 'order_items';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
