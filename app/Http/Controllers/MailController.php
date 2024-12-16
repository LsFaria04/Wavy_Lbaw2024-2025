<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MailModel;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Mail;

class MailController extends Controller
{
    
    function sendPasswordReset(Request $request) {

        $user = User::where('email', $request->email)->firstOrFail();

        $token = Str::random(32); //creates a 32 char long token to send

        if($user === null){
            return response()->json(['message' => 'Email doens not exist', 'response' => '404']);
        }

        $mailData = [
            'email' => $request->email,
            'token' => $token
        ];

        try{
            info(strval($request->email));
            info(strval($token));
            Mail::to($request->email)->send(new MailModel($mailData));

        } catch(\Exception $e){
            return response()->json(['message' => 'Could not send the email', 'response' => '500']);
        }

        $user->passwordhash = Hash::make($token);
        $user->save();

        return response()->json(['message' => 'Email successfully sended', 'response' => '200']);
    }

}
