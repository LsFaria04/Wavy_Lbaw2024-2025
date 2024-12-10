<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupInvitation extends Model
{
    use HasFactory;

    protected $table = 'group_invitation';

    protected $primaryKey = 'invitationid';

    public $timestamps = false;

    protected $fillable = [
        'invitationid',
        'groupid',
        'userid',
        'createddate',
    ];

    protected $hidden = [
        'userid',
        'groupid',
    ];

    protected $dates = [
        'createddate',
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

    public function getCreatedDateAttribute($value)
    {
        return Carbon::parse($value);
    }
}
