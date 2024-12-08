<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $primaryKey = 'notificationid';

    public $timestamps = false;

    protected $fillable = [
        'receiverid', 'date', 'seen', 'followid', 'commentid', 'likeid',
    ];

    // Relacionamento com o comentário
    public function comment() {
        return $this->belongsTo(Comment::class, 'commentid');
    }

    // Relacionamento com o post
    public function post() {
        return $this->hasOneThrough(Post::class, Comment::class, 'commentID', 'postID', 'commentID', 'postID');
    }
}
