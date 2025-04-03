<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $guarded = [];
    protected $table = 'user_addresses';

    public function city(){
        return $this->belongsTo(City::class);
    }
    public function province(){
        return $this->belongsTo(Province::class);
    }
}
