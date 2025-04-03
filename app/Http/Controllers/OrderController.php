<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public static function create($cart , $addressId , $coupon , $amounts , $trackId)
    {

        DB::beginTransaction();

        $order = Order::create([
            'user_id' => auth()->id(),
            'address_id' => $addressId,
            'total_amount' => $amounts['totalAmount'],
            'coupon_amount' => $amounts['couponAmount'],
            'paying_amount' => $amounts['payAmount'],
            'coupon_id' => $coupon == null ? null : $coupon->id
        ]);

        foreach ($cart as $key => $orderItem) {
            $product = Product::findOrFail($key);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->is_discounted ? $product->discount_price : $product->price,
                'quantity' => $orderItem['qty'],
                'subtotal' => $product->is_discounted ? ($product->discount_price * $orderItem['qty']) : ($product->price * $orderItem['qty'])
            ]);
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'amount' => $amounts['payAmount'],
            'token' => $trackId
        ]);

        DB::commit();


    }

    public static function update($trackId)
    {
        DB::beginTransaction();

        $transaction = Transaction::where('token', $trackId)->firstOrFail();

        $transaction->update([
            'status' => 1,
            'ref_number' => $trackId
        ]);

        $order = Order::findOrFail($transaction->order_id);

        $order->update([
            'status' => 1,
            'payment_status' => 1
        ]);

        $orderItems = OrderItem::where('order_id', $order->id)->get();

        foreach ($orderItems as $item) {
            $product = Product::find($item->product_id);
            $product->update([
                'quantity' => ($product->quantity -  $item->quantity)
            ]);
        }

        DB::commit();

    }
}
