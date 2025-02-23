<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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
use App\Http\Controllers\GroupListController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ReportController;

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
Route::post('/profile/{userid}/follow', [ProfileController::class, 'follow'])->name('follow')->middleware('auth');
Route::post('/profile/{userid}/unfollow', [ProfileController::class, 'unfollow'])->name('unfollow')->middleware('auth');


// API
Route::controller(PostController::class)->group(function () {
    Route::get('api/posts','getPostsTimeline' );
    Route::get('api/posts/{username}', 'getUserPosts');
    Route::get('api/comments/post/{id}', 'show');
});
Route::get('api/search', [SearchController::class, 'search']);
Route::post('api/search/filtered', [SearchController::class, 'search']);
Route::get('api/comments/{username}', [CommentController::class, 'getUserCommentsByUsername']);
Route::get('api/likes/{username}', [LikeController::class,'getUserLikesByUsername']);
Route::get('api/{username}', [ProfileController::class, 'getProfileUserData']);
Route::post('/api/profile/{id}/delete', [ProfileController::class, 'delete']);
Route::post('api/auth-check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});
Route::post('api/auth-id', function () {
    return response()->json(['id' => auth()->id(), 'isadmin' => Auth::user()->isadmin, 'username' => Auth::user()->username]);
});
Route::get('/api/groups/{id}', [GroupController::class, 'getGroupData']);
Route::get('/api/groups/{id}/posts', [GroupController::class, 'getGroupPosts']);
Route::get('/api/groups/{id}/members', [GroupController::class, 'getGroupMembers']);
Route::get('/api/groups/{id}/invitations', [GroupController::class, 'getGroupInvitations']);
Route::get('/api/groups/{id}/requests', [GroupController::class, 'getJoinRequests']);
Route::post('/api/groups/{groupid}/invitations', [GroupController::class, 'sendInvitation']);
Route::delete('/api/groups/{group}/invitations/{invitation}', [GroupController::class, 'cancelInvitation']);
Route::post('/api/groups/{group}/requests', [GroupController::class, 'sendJoinRequest'])->middleware('auth');
Route::post('/api/groups/{group}/requests/{request}/reject', [GroupController::class, 'rejectJoinRequest']);
Route::post('/api/groups/{group}/requests/{request}/accept', [GroupController::class, 'acceptJoinRequest']);
Route::post('/groups/{groupid}/invitations/{invitationid}/accept', [GroupController::class, 'acceptInvitation']);
Route::post('/groups/{groupid}/invitations/{invitationid}/reject', [GroupController::class, 'rejectInvitation']);
Route::controller(TopicController::class)->group(function () {
    Route::get('/api/topics/all/{postid}', 'getAllTopicsToPost');
    Route::get('/api/topics/search/all/{postid}', 'searchAllTopicsToPost');
    Route::get('/api/topics/all', 'getAllTopics');
    Route::get('/api/topics/search/all', 'searchAllTopics');
    Route::get('/api/topics/{userid}', 'getUserTopics');
    Route::get('/api/topics/canAdd/{userid}', 'getTopicsToAdd');
    Route::get('/api/topics/search/{userid}', 'searchUserTopic');
    Route::get('/api/topics/search/canAdd/{userid}', 'searchTopicsToAdd');
    Route::put('/api/topics/add/{topicid}/{userid}', 'addTopicToUser');
    Route::delete('/api/topics/remove/{topicid}/{userid}', 'removeTopicFromUser');
    Route::post('/api/topics/add', 'create');
    Route::post('/api/topics/delete/{topicid}', 'delete');
    
});
Route::get('/api/reports/all', [ReportController::class, 'getReports']);
Route::get('/api/reports/search/all', [ReportController::class, 'searchReports']);
Route::post('/api/reports/delete/{reportid}', [ReportController::class, 'delete']);
Route::post('/api/reports/create', [ReportController::class, 'create']);
Route::post('/api/admin/users/create', [AdminController::class, 'storeUser']);
Route::post('/api/admin/users/ban/{userid}', [AdminController::class, 'banUser']);
Route::get('/api/admin/users/all', [AdminController::class, 'getUsersForAdmin']);
Route::get('/api/admin/users/search/all', [AdminController::class, 'searchUsersForAdmin']);
Route::post('/api/notifications', [NotificationController::class, 'getNotifications']);
Route::post('/api/profile/followrequest/{userid}', [ProfileController::class, 'getFollowRequests']);
Route::post('/api/profile/followrequest/accept/{userid}', [ProfileController::class, 'acceptFollowRequest']);
Route::post('/api/profile/followrequest/reject/{userid}', [ProfileController::class, 'rejectFollowRequest']);
Route::post('/api/profile/follows/{userid}', [ProfileController::class, 'getFollows']);
Route::post('/api/profile/followers/{userid}', [ProfileController::class, 'getFollowers']);
Route::post('/api/contact/submit', [MailController::class, 'sendContactMessage']);

//Reports
Route::post('/reports/delete/{reportid}', [ReportController::class, 'delete']);

//Topics
Route::controller(TopicController::class)->group(function () {
    Route::post('/topics/add', 'create');
    Route::post('/topics/delete/{topicid}', 'delete');
});


//Posts
Route::get('/home', [PostController::class, 'getPostsTimeline'])->name('home');
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
Route::post('/posts/update/{post}', [PostController::class, 'update'])->name('posts.update');
Route::post('/posts/delete/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
Route::post('/like-post/{postId}', [PostController::class, 'likePost'])->name('likePost');


//Comments
Route::post('/comments/update/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::post('/comments/delete/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::post('/comments/store', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comments/storeSubcomment', [CommentController::class, 'storeSubcomment'])->name('comments.storeSubcomment');
Route::post('/like-comment/{commentId}', [CommentController::class, 'likeComment'])->name('likeComment');

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
Route::view('/forgot-password', 'auth.recoverPassword')->name('forgotPassword');
Route::post('/forgot-password', [MailController::class, 'sendPasswordReset']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Search
Route::get('/search', [SearchController::class, 'search'])->name('search');

// Admin
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
});
    Route::get('/admin/users/create', [AdminController::class, 'storeUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

// Group
Route::get('/groups/{groupname}', [GroupController::class, 'show'])->name('group');
Route::delete('/groups/{groupid}/leave', [GroupController::class, 'leaveGroup'])->name('group.leave');
Route::delete('/groups/{groupid}/remove/{userid}', [GroupController::class, 'removeMember'])->name('group.removeMember');
Route::put('/groups/{groupid}', [GroupController::class, 'update'])->name('group.update');
Route::get('/groups', [GroupListController::class, 'index'])->name('groupList');
Route::post('/groups', [GroupController::class, 'store'])->name('group.store');
Route::delete('/groups/{groupid}', [GroupController::class, 'deleteGroup'])->name('groups.delete');

//About Us
Route::view('/about', 'pages.about')->name('about');

//Contacts
Route::view('/contacts', 'pages.contacts')->name('contacts');


//Main Features
Route::view('/features', 'pages.features')->name('features');

//Notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

//Pusher Authentication Route for Public Channels Private Channels

Route::get('/pusher/auth', function (Illuminate\Http\Request $request) {
    if (auth()->check()) {
        return Broadcast::auth($request);
    } else {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
})->middleware('auth')->name('pusher.auth');




