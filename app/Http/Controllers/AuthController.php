<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cellphone' => ['required', 'regex:/^09[0|1|2|3][0-9]{8}$/'],
        ]);

        $user = User::where('cellphone', $request['cellphone'])->first();
        $otp = mt_rand(100000, 999999);
        $loginToken = Hash::make($otp);

        try {
            if ($user) {
                $user->update([
                    'login_token' => $loginToken,
                    'otp' => $otp,
                ]);

            } else {

                $user = User::create([
                    'cellphone' => $request['cellphone'],
                    'login_token' => $loginToken,
                    'otp' => $otp,
                ]);

            }

            return response()->json(['login_token' => $loginToken , 'otp' => $otp]);


        } catch (Exception $e) {

            return response()->json(['errors' => $e->getMessage()], 500);
        }


    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home.index');
    }

    public function submitOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
            'login_token' => ['required', 'string'],
        ]);

        try {

            $user = User::where('login_token', $request['login_token'])->firstOrFail();
            if ($user->otp == $request['otp']) {
                auth()->login($user , true);
                return response()->json(['message' => 'OTP Verified Successfully']);
            } else {
                return response()->json(['message' => 'کد ورود اشتباه است!'] , 423);
            }

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }


    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'login_token' => "required",
        ]);

        $user = User::where('login_token', $request['login_token'])->firstOrFail();
        $otp = mt_rand(100000, 999999);
        $loginToken = Hash::make($otp);

        try {

            $user->update([
                'login_token' => $loginToken,
                'otp' => $otp,
            ]);

            return response()->json(['login_token' => $loginToken]);

        } catch (Exception $e) {

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
