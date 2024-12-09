<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
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
            'commentid',
            'postid',   
            'commentid',
            'postid'    
        );
    }
    
}
