<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMembership extends Model
{
    use HasFactory;

    protected $table = 'group_membership';

    protected $primaryKey = 'memberid';

    public $timestamps = false;

    protected $fillable = [
        'memberid',
        'groupid',
        'userid',
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
