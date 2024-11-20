<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        $posts = Post::all();
        $users = User::all();
        return view('pages.admin', compact('posts', 'users'));
    }
}
