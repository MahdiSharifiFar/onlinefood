<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $addresses = auth()->user()->addresses;
        $cart = $request->session()->get('cart', []);
        $cart = array_reverse($cart, true);

        if($cart == null) {
            return view('cart.index', compact('cart'));
        }

        $cart_total_price = 0;
        foreach($cart as $key => $item){
            $price = $item['is_discount'] ? $item['discount_price'] : $item['price'];
            $cart_total_price += $price * $item['qty'];
        }

        return view('cart.index', compact('cart', 'cart_total_price', 'addresses') );
    }

    public function increment(Request $request)
    {
        $request->validate([
            //'qty' => 'required|integer|min:1',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $product = Product::where('id', $request['product_id'])->first();
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {

            if ($cart[$product->id]['qty'] >= $product->quantity) {
                return back()->with(['error' => 'موجودی محصول مورد نظر به اتمام رسیده است!']);
            }

            $cart[$product->id]['qty']++;

        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'is_discount' => $product->is_discounted,
                'primary_image' => $product->primary_image,
                'qty' => 1
            ];
        }

        $request->session()->put('cart', $cart);
        return back()->with(['success' => 'محصول به سبد خرید اضافه شد']);
    }

    public function decrement(Request $request)
    {
        $request->validate([
            //'qty' => 'required|integer|min:1',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $product = Product::where('id', $request['product_id'])->first();
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {

            if ($cart[$product->id]['qty'] > 1) {

                $cart[$product->id]['qty']--;
                $request->session()->put('cart', $cart);
                return back()->with(['success' => 'تعداد محصول مورد نظر باموفقیت کاهش یافت']);

            } else {
                return back()->with(['error' => 'تعداد صفر برای محصول مورد نظر مجاز نمیباشد!']);
            }

        } else {

            return back()->with(['error' => 'محصول مورد نظر در سبد خرید شما موجود نیست!']);
        }

    }

    public function clear(Request $request)
    {
        $request->session()->put('cart', []);
        return redirect()->route('products.menu')->with(['success' => 'سبد خرید شما با موفقیت پاک شد']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$request['product_id']])) {

            unset($cart[$request['product_id']]);
            $request->session()->put('cart', $cart);
            return back()->with(['success' => 'محصول مورد نظر از سبد خرید شما حذف شد']);

        } else {
            return back()->with(['error' => 'محصول مورد نظر در سبد خرید شما موجود نیست!']);
        }

    }

    public function add(Request $request)
    {

        $request->validate([
            'qty' => 'required|integer|min:1',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $product = Product::where('id', $request['product_id'])->first();
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {

            $totalQty = ($cart[$product->id]['qty'] + $request['qty']);
            if ($totalQty >= $product->quantity) {
                return back()->with(['error' => "تعداد محصول $product->name بیش از حد مجاز است! "]);
            }

            $cart[$product->id]['qty'] += $request['qty'];

        } else {

            if ($request['qty'] > $product->quantity) {
                return back()->with(['error' => "تعداد محصول $product->name بیش از حد مجاز است! "]);
            }

            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'is_discount' => $product->is_discounted,
                'primary_image' => $product->primary_image,
                'qty' => $request['qty']
            ];
        }

        $request->session()->put('cart', $cart);
        return back()->with(['success' => 'محصول به سبد خرید اضافه شد']);
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request['code'])->where('expires_at', '>', Carbon::now())->first();

        if ($coupon == null) {
            return back()->withErrors(['code' => 'کد تخفیف نامعتبر است!']);
        }

        if ( $coupon->isUsed(auth()->id()) ) {
            return redirect()->route('cart.index')->withErrors(['code' => 'این کد تخفیف قبلا استفاده شده است!']);
        }

        $request->session()->put('coupon', ['code' => $coupon->code, 'percent' => $coupon->percentage]);
        return back();
    }

}
