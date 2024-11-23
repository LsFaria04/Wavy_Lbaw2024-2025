<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $casts = [
        'createdDate' => 'datetime',
    ];

    protected $primaryKey = 'likeid';

    // Don't add create and update timestamps in database.
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userid',
        'createddate',
        'postid',        
        'commentid',
    ];

    protected $dates = [
        'createddate',
    ];

    public function posts(): Belongsto
    {
        return $this->belongsTo(Post::class);
    }

    public function comments(): Belongsto
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): Belongsto
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value); // Ensure it's a Carbon instance
    }
}
