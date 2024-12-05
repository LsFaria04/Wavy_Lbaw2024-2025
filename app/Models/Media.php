<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $primaryKey = 'mediaid';

    // Don't add create and update timestamps in database.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'postid',
        'commentid',
        'userid',
        'path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'postid',
        'commentid',
        'userid',
    ];

    /**
     * Relationship with the User model.
     */
    public function user(): BelongsTo
    {
        // Establishes the belongsTo relationship with User
        return $this->belongsTo(User::class, 'userid', 'id');
    }

    /**
     * Relationship with the Post model.
     */
    public function post(): BelongsTo
    {
        // Establishes the belongsTo relationship with Post
        return $this->belongsTo(Post::class, 'postid', 'postid');
    }

    /**
     * Relationship with the Comment model.
     */
    
     public function comment(): BelongsTo
     {
         return $this->belongsTo(Comment::class, 'commentid', 'commentid');
     }
}
