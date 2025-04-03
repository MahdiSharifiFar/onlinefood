<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $table = 'orders';
    protected $guarded = [];

    public function getStatusAttribute($status)
    {
        switch ($status) {
            case '0':
                $status = 'در انتظار پرداخت';
                break;
            case '1':
                $status = 'در حال پردازش';
                break;
            case '2':
                $status = 'ارسال شد';
                break;
            case '3':
                $status = 'لغو شد';
                break;
            case '4':
                $status = 'مرجوعی';
                break;
        }
        return $status;
    }

    public function getPaymentStatusAttribute($paymentStatus)
    {
        switch ($paymentStatus) {
            case '0':
                $paymentStatus = 'ناموفق';
                break;
            case '1':
                $paymentStatus = 'موفق';
                break;
        }
        return $paymentStatus;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id');
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
