<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model {
    use HasFactory;

    protected $table = 'follow';

    const STATE_PENDING = 'Pending';
    const STATE_ACCEPTED = 'Accepted';
    const STATE_REJECTED = 'Rejected';  

    protected $casts = [
        'followdate' => 'datetime',
    ];

    public $timestamps = false;

    protected $fillable = [
        'followerid',
        'followeeid',
        'state',
        'followdate',
    ];

    public $incrementing = false;

    protected $primaryKey = ['followerid', 'followeeid'];


    public function follower() {
        return $this->belongsTo(User::class, 'followerid');
    }

    public function followee() {
        return $this->belongsTo(User::class, 'followeeid');
    }

    public function scopePending($query) {
        return $query->where('state', self::STATE_PENDING);
    }

    public function scopeAccepted($query) {
        return $query->where('state', self::STATE_ACCEPTED);
    }

    public static function isFollowing(int $followerId, int $followeeId): bool {
        return self::where('followerid', $followerId)
                   ->where('followeeid', $followeeId)
                   ->where('state', self::STATE_ACCEPTED)
                   ->exists();
    }

    public static function isPending(int $followerId, int $followeeId): bool {
        return self::where('followerid', $followerId)
                   ->where('followeeid', $followeeId)
                   ->where('state', self::STATE_PENDING)
                   ->exists();
    }
}
