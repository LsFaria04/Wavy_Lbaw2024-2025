<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    protected $primaryKey = 'commentid';

    // Don't add create and update timestamps in database.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userid',
        'message',
        'createddate',
        'postid',        
        'parentcommentid',
    ];

    protected $dates = [
        'createddate',
    ];

    /**
     * Relationship with User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    /**
     * Relationship with Post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'postid', 'postid');
    }

    /**
     * Relationship with parent comment (for nested comments).
     */
    public function parentComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parentcommentid', 'commentid');
    }
}