<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    //protected $appends = ['used'];
    protected $table = 'coupons';

    public function isUsed($user_id)
    {
        return Order::where('user_id', $user_id)->where('payment_status', 1)->exists();
    }

}
