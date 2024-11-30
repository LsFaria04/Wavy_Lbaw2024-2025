<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinGroupRequest extends Model
{
    use HasFactory;

    protected $table = 'join_group_request';

    public $timestamps = false;

    protected $fillable = [
        'groupid',
        'userid',
        'date',
        'state',
    ];
}
