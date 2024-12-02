<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $primaryKey = 'groupid';

    public $timestamps = false;

    protected $fillable = [
        'groupname',
        'description',
        'visibilitypublic',
        'ownerid',
    ];

    /**
     * Group owner relationship.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'ownerid');
    }

    /**
     * Group members relationship.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, GroupMembership::class, 'groupid', 'userid');
    }

    /**
     * Join requests for the group.
     */
    public function joinRequests()
    {
        return $this->hasMany(JoinGroupRequest::class, 'groupid');
    }

    /**
     * Posts related to the group.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'groupid');
    }
}
