<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
           'fullName' => 'required|string',
           'email' => 'required|email',
           'subject' => 'required|string',
           'body' => 'required|string',
        ]);

        ContactUs::create([
            'full_name' => $request['fullName'],
            'email' => $request['email'],
            'subject' => $request['subject'],
            'body' => $request['body'],
        ]);

        return back()->with('success', 'متن پیام شما با موفقیت ارسال شد');

    }

}
