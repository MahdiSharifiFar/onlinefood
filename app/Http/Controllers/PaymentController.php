<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function send(Request $request)
    {
        $request->validate([
            'address_id' => 'required|integer',
            'coupon' => 'nullable|string'
        ]);

        if (!$request->session()->has('cart')) {
            return redirect()->route('cart.index')->with(['error' => 'سبد خرید شما خالی است!']);
        }

        $cart = $request->session()->get('cart');
        $totalAmount = 0;

        foreach ($cart as $key => $item) {
            $product = Product::findOrFail($key);
            if ($product->quantity < $item['qty']) {
                return redirect()->route('cart.index')->with(['error' => 'تعداد محصول وارد شده اشتباه است!']);
            }

            $totalAmount += $product->is_discounted ? $product->discount_price * $item['qty'] : $product->price * $item['qty'];
        }

        $couponAmount = 0;
        $coupon = null;

        if ($request['coupon']) {

            $coupon = Coupon::where('code', $request['coupon'])->where('expires_at', '>', Carbon::now())->first();

            if ($coupon == null) {
                return redirect()->route('cart.index')->withErrors(['code' => 'کد تخفیف نامعتبر است!']);
            }

            if ($coupon->isUsed(auth()->id())) {
                return redirect()->route('cart.index')->withErrors(['code' => 'این کد تخفیف قبلا استفاده شده است!']);
            }

            $couponAmount = ($totalAmount * $coupon->percentage) / 100;

        }

        $payAmount = $totalAmount - $couponAmount;

        $amounts = [
            'payAmount' => $payAmount,
            'couponAmount' => $couponAmount,
            'totalAmount' => $totalAmount,
        ];

        $merchant = env('ZIBAL_API_MERCHANT');
        $callbackUrl = env('ZIBAL_CALLBACK_URL');

        $res = $this->sendRequest($merchant, $payAmount . '0', $callbackUrl);
        $res = json_decode($res, true);
        $trackId = $res['trackId'];

        if ($res['result'] === 100) {

            OrderController::create($cart, $request['address_id'], $coupon, $amounts, $trackId);

            return redirect("https://gateway.zibal.ir/start/$trackId");
        } else {

            return redirect()->route('cart.index')->with(['error' => 'عملیات پرداخت با خطا مواجه شد!']);

        }

    }

    public function verify(Request $request)
    {
        $request->validate([
            'success' => 'required|integer',
            'status' => 'required|integer',
            'trackId' => 'required|string'
        ]);

        $trackId = $request['trackId'];
        $res = $this->verifyRequest(env('ZIBAL_API_MERCHANT'), $trackId);
        $res = json_decode($res, true);

        if ($res['result'] === 100) {

            OrderController::update($trackId);
            request()->session()->put('cart', []);
            return redirect()->route('payment.status', ['status' => 1 , 'trackId' => $trackId] );

        } else {

            return redirect()->route('payment.status', ['status' => 0] );

        }

    }

    public function status(Request $request)
    {
        $request->validate([
            'trackId' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        $trackId = $request['trackId'] ?? null;
        $status = $request['status'];

        return view('payment.status', ['trackId' => $trackId, 'status' => $status]);
    }

    public function sendRequest($merchant, $amount, $callbackUrl, $mobile = null, $factorNumber = null, $description = null)
    {
        return $this->curl_post('https://gateway.zibal.ir/v1/request', [
            'merchant' => $merchant,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl,
            'mobile' => $mobile,
            'factorNumber' => $factorNumber,
            'description' => $description,
        ]);
    }

    function verifyRequest($api, $token)
    {
        return $this->curl_post('https://gateway.zibal.ir/v1/verify', [
            'merchant' => $api,
            'trackId' => $token,
        ]);
    }

    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }


}
