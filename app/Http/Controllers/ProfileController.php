<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Favorites;
use App\Models\Product;
use App\Models\Province;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request['name'],
            'email' => $request['email'],
        ]);

        return back()->with(['success' => 'اطلاعات پروفایل شما با موفقیت آپدیت شد']);

    }

    public function showAddress()
    {
        $addresses = auth()->user()->addresses;
        return view('profile.addresses.index', compact('addresses'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with( [ 'address' , 'items'])->orderByDesc('created_at')->paginate(4);
        return view('profile.orders.index', compact('orders'));
    }

    public function transactions()
    {
        $transactions = auth()->user()->transactions()->orderByDesc('created_at')->paginate(4);
        return view('profile.transactions.index', compact('transactions'));
    }

    public function createAddress()
    {
        $cities = City::all();
        $provinces = Province::all();
        return view('profile.addresses.create' , compact('cities', 'provinces'));
    }

    public function storeAddress(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'title' => 'required|string',
            'cellphone' => ['required', 'regex:/^09[0|1|2|3][0-9]{8}$/'],
            'postal_code' => ['required', 'regex:/^\d{5}[ -]?\d{5}$/'],
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'address' => 'required|string'
        ]);

        UserAddress::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'cellphone' => $request->cellphone,
            'postal_code' => $request->postal_code,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
        ]);

        return redirect()->route('profile.showAddress')->with('success', 'آدرس شما با موفقیت ثبت شد');
    }

    public function editAddress(UserAddress $address)
    {
        $cities = City::all();
        $provinces = Province::all();
        return view('profile.addresses.edit', compact('address', 'cities', 'provinces'));
    }

    public function updateAddress(Request $request, UserAddress $address)
    {
        $request->validate([
            'title' => 'required|string',
            'cellphone' => ['required', 'regex:/^09[0|1|2|3][0-9]{8}$/'],
            'postal_code' => ['required', 'regex:/^\d{5}[ -]?\d{5}$/'],
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'address' => 'required|string'
        ]);

        $address->update([
            'title' => $request['title'],
            'cellphone' => $request['cellphone'],
            'postal_code' => $request['postal_code'],
            'province_id' => $request['province_id'],
            'city_id' => $request['city_id'],
            'address' => $request['address'],
        ]);

        return redirect()->route('profile.showAddress')->with('success', 'آدرس شما با موفقیت ویرایش شد');
    }

    public function addToFavorites(Product $product)
    {
        if (!auth()->check()) {
            return back()->with(['warning' => 'برای دسترسی به لیست علاقه مندی ابتدا وارد سیستم شوید!']);
        }

        Favorites::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id
        ]);

        return back()->with(['success' => 'محصول با موفقیت به لیست علاقه مندی اضافه شد']);
    }

    public function favorites()
    {
        $favorites = auth()->user()->favorites->reverse();
        return view('profile.favorites.index', compact('favorites'));
    }

    public function removeFavorite(Favorites $favorite)
    {
        $favorite->delete();
        return back()->with(['warning' => 'محصول از لیست علاقه مندی حذف شد']);
    }

}
