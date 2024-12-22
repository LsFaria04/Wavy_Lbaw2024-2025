<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\ValidationException;

use App\Models\User;


class ResetPasswordController extends Controller
{
    function resetPassword(Request $request) {

        try{
        $request->validate([
            'password' => 'required|min:8|confirmed'
            ]
        );
        }catch(ValidationException $e) {
            return response()->json(['message' => 'Password has less than 8 characters or confirmation does not match', 'response' => '403']);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if(!Hash::check($request->token, $user->passwordhash)) {
            return response()->json(['message' => 'Wrong Token', 'response' => '403']);
        }

        $user->passwordhash = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password reseted successfully', 'response' => '200']);
    }

}