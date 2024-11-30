<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $primaryKey = 'groupid';

    public $timestamps = false;

    protected $fillable = [
        'groupname',
        'description',
        'visibility_public',
        'owner',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ownerid');
    }

    public function members(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, GroupMembership::class, 'groupid', 'userid');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(JoinGroupRequest::class, 'groupid');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(GroupInvitation::class, 'groupid');
    }
}
