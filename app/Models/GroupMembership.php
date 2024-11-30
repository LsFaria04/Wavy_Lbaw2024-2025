<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMembership extends Model
{
    use HasFactory;

    protected $table = 'group_membership';

    public $timestamps = false;

    protected $fillable = [
        'groupid',
        'userid',
    ];
}
