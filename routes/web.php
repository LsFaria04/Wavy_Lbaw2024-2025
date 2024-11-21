<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CardController;
use App\Http\Controllers\PostController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/home');
Route::view('/home', 'pages.home')->name('home');

//Profile
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile');
Route::put('/profile/{userid}', [ProfileController::class, 'update'])->name('profile.update');

// Cards
Route::controller(CardController::class)->group(function () {
    Route::get('/cards', 'list')->name('cards');
    Route::get('/cards/{id}', 'show');
});


// API
Route::controller(CardController::class)->group(function () {
    Route::put('/api/cards', 'create');
    Route::delete('/api/cards/{card_id}', 'delete');
});
Route::controller(PostController::class)->group(function (){
    Route::get('api/posts','getPostPagination' );
});
Route::post('/auth-check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});
Route::post('/auth-id', function () {
    return response()->json(['id' => auth()->id()]);
});

//Posts
Route::get('/home', [PostController::class, 'showFirstSet'])->name('home');
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
Route::post('/posts/update/{post}', [PostController::class, 'update'])->name('posts.update');
Route::post('/posts/delete/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

use App\Http\Controllers\MediaController;

//Media
Route::post('/media/store', [MediaController::class, 'store'])->name('media.store');
Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Search
Route::get('/search', [SearchController::class, 'search'])->name('search');

// Admin
Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'isAdmin']);


