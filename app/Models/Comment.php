<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    protected $casts = [
        'createdDate' => 'datetime',
    ];

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
     * Get the likes made by the user.
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'userid', 'userid');
    }

    /**
     * Relationship with parent comment (for nested comments).
     */
    public function parentComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parentcommentid', 'commentid');
    }

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value); // Ensure it's a Carbon instance
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'commentid');
    }
}