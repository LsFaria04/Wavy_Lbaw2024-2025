<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinGroupRequest extends Model
{
    use HasFactory;

    protected $table = 'join_group_request';

    protected $casts = [
        'date' => 'datetime',
    ];

    public $timestamps = false;

    protected $fillable = [
        'groupid',
        'userid',
        'date',
        'state',
    ];

    /**
     * The group this invitation is for.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'groupid');
    }

    /**
     * The user this invitation is sent to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value);
    }
}
