<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function post(): Belongsto
    {
        return $this->belongsTo(Post::class, 'postid', 'postid');
    }

    public function comment(): Belongsto
    {
        return $this->belongsTo(Comment::class, 'commentid', 'commentid');
    }

    public function user(): Belongsto
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value);
    }
}
