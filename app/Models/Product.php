<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    protected $appends = ['is_discounted'];

    public function getIsDiscountedAttribute()
    {
        return $this->quantity > 0 and $this->active and $this->discount_price != null and $this->discount_price != 0 and $this->start_date_discount <= now() and $this->end_date_discount >= now();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . trim($search) . '%')->orWhere('description', 'like', '%' . trim($search) . '%');
    }

    public function scopeFilter($query)
    {
        if (request()->has('category')) {
            $query->where('category_id', request()->category);
        }

        if (request()->has('sortBy')) {
            switch (request()->sortBy) {
                case 'max':
                    $query->orderBy('price', 'desc');
                    break;

                case 'min':
                    $query->orderBy('price', 'asc');
                    break;

                case 'bestseller':
                    $orders = Order::where('payment_status' , 1)->with('products')->get();
                    $productIds = [];
                    foreach($orders as $order){
                        foreach($order->products as $product){
                            array_push($productIds, $product->id);
                        }
                    }

                    // dd($productIds, array_count_values($productIds), array_keys(array_count_values($productIds)));
                    $query->whereIn('id', array_keys(array_count_values($productIds)));
                    break;

                case 'discount':
                    $query->where('discount_price', '>', 0)->where('start_date_discount', '<=', now())->where('end_date_discount', '>=', now())->orderBy('discount_price', 'desc');
                    break;

                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }else {
            $query->orderBy('id', 'desc');
        }

        return $query;
    }

}
