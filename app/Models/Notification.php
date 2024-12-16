<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model {
    use HasFactory;

    protected $table = 'notification';

    protected $primaryKey = 'notificationid';

    public $timestamps = false;

    protected $fillable = [
        'receiverid', 'date', 'seen', 'followid', 'commentid', 'likeid',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function comment() {
        return $this->belongsTo(Comment::class, 'commentid');
    }

    public function post() {
        return $this->hasOneThrough(
            Post::class,
            Comment::class,
            'commentid', // Foreign key in Comment
            'postid',   // Foreign key in Post
            'commentid', // Local key in Notification
            'postid'    // Local key in Comment
        );
    }

    public function like() {
        return $this->belongsTo(Like::class, 'likeid');
    }

    public function follower() {
        return $this->belongsTo(User::class, 'followerid');
    }

    public function followee() {
        return $this->belongsTo(User::class, 'followeeid');
    }
}
