<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model {
    use HasFactory;

    protected $table = 'follow';

    const STATE_PENDING = 'pending';
    const STATE_APPROVED = 'approved';

    const REQUESTSTATE_PENDING = 'pending';
    const REQUESTSTATE_APPROVED = 'approved';

    protected $casts = [
        'followdate' => 'datetime',
    ];

    public $timestamps = false;

    protected $fillable = [
        'follower_id',
        'followee_id',
        'state',
        'requeststate',
        'followdate',
    ];

    public function follower() {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function followee() {
        return $this->belongsTo(User::class, 'followee_id');
    }

    public function scopePending($query) {
        return $query->where('requeststate', self::REQUESTSTATE_PENDING);
    }

    public function scopeApproved($query) {
        return $query->where('requeststate', self::REQUESTSTATE_APPROVED);
    }

    public function isFollowing(User $user) {
        return $this->where('follower_id', $this->follower_id)
                    ->where('followee_id', $user->id)
                    ->exists();
    }
}
