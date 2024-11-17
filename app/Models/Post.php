<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';

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

    public function user(): BelongsTo
    {
        // Establishes the belongsTo relationship with User
        return $this->belongsTo(User::class, 'userid');
    }
}
