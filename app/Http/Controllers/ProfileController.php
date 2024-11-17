<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($username) {
        $user = User::where('username', $username)->firstOrFail();
        return view('pages.profile', compact('user'));
    }
}
