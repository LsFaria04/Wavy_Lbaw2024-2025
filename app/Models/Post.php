<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';
 
    protected $casts = [
        'createdDate' => 'datetime',
    ];

    protected $primaryKey = 'postid';

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
        'visibilitypublic',
        'createddate',
        'groupid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'userid',
        'groupid',
    ];

    protected $dates = [
        'createddate',
    ];

    public function user()
    {
        // Establishes the belongsTo relationship with User
        return $this->belongsTo(User::class, 'userid');
    }

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value); // Ensure it's a Carbon instance
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'postid');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class, 'postid');
    }

}
