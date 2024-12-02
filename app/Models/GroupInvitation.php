<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    use HasFactory;

    protected $table = 'group_invitation';

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
}
