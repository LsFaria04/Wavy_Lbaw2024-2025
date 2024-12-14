<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MailModel;
use Mail;

class MailController extends Controller
{
    function send(Request $request) {

        $mailData = [
            'email' => $request->email,
        ];

        Mail::to($request->email)->send(new MailModel($mailData));
        return redirect()->route('home');
    }

}
