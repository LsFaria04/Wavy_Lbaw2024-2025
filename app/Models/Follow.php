<?php

// app/Models/Follow.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model {
    use HasFactory;

    protected $table = 'follow';

    protected $casts = [
        'followdate' => 'datetime',
    ];

    public $timestamps = false;

    protected $fillable = [
        'followerid',
        'followeeid',
        'state',
        'requeststate',
        'followdate',
    ];

    public function follower() {
        return $this->belongsTo(User::class, 'followerid');
    }

    public function followee() {
        return $this->belongsTo(User::class, 'followeeid');
    }

    public function scopePending($query) {
        return $query->where('requeststate', 'pending');
    }

    public function scopeApproved($query) {
        return $query->where('requeststate', 'approved');
    }
}
