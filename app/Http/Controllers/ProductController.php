<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $randomProducts = Product::where('quantity' , '>' , 0)->where('active' , 1)->get()->random(4);
        return view('products.show', ['product' => $product , 'randomProducts' => $randomProducts]);
    }

    public function menu(Request $request)
    {
        $categories = Category::all();
        $search = $request['search'];
        $products = Product::where('quantity' , '>' , 0)->where('active' , 1)->search($search)->filter()->paginate(6);

        return view('products.menu' , compact('products' , 'categories'));
    }

}
