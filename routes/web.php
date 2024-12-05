<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\GroupController;

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
Route::delete('/profile/{id}/delete', [ProfileController::class, 'delete'])->name('profile.delete');

// API
Route::controller(PostController::class)->group(function (){
    Route::get('api/posts','getPostsTimeline' );
    Route::get('api/posts/{username}', 'getUserPosts');
});
Route::get('api/search', [SearchController::class, 'search']);
Route::get('api/comments/{username}', [CommentController::class, 'getUserCommentsByUsername']);
Route::get('api/likes/{username}', [LikeController::class,'getUserLikesByUsername']);
Route::get('api/{username}', [ProfileController::class, 'getProfileUserData']);
Route::post('api/auth-check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});
Route::post('api/auth-id', function () {
    return response()->json(['id' => auth()->id(), 'isadmin' => Auth::user()->isadmin]);
});
Route::get('/api/groups/{id}', [GroupController::class, 'getGroupData']);
Route::get('/api/groups/{id}/posts', [GroupController::class, 'getGroupPosts']);
Route::get('/api/groups/{id}/members', [GroupController::class, 'getGroupMembers']);
Route::get('/api/groups/{id}/invitations', [GroupController::class, 'getGroupInvitations']);
Route::get('/api/groups/{id}/requests', [GroupController::class, 'getJoinRequests']);
Route::delete('/api/groups/{group}/invitations/{invitation}', [GroupController::class, 'cancelInvitation']);
Route::post('/api/groups/{group}/requests/{request}/reject', [GroupController::class, 'rejectJoinRequest']);
Route::post('/api/groups/{group}/requests/{request}/accept', [GroupController::class, 'acceptJoinRequest']);

//Posts
Route::get('/home', [PostController::class, 'getPostsTimeline'])->name('home');
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
Route::post('/posts/update/{post}', [PostController::class, 'update'])->name('posts.update');
Route::post('/posts/delete/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

//Comments
Route::post('/comments/update/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::post('/comments/delete/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::get('/comments/{id}', [CommentController::class, 'show'])->name('comments.show');
Route::post('/comments/store', [CommentController::class, 'store'])->name('comments.store');

//Media
Route::post('/media/store', [MediaController::class, 'store'])->name('media.store');

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
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
});
    Route::get('/admin/users/create', [AdminController::class, 'storeUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

// Group
Route::get('/group/{id}', [GroupController::class, 'show'])->name('group');

//About Us
Route::view('/about', 'pages.about')->name('about');