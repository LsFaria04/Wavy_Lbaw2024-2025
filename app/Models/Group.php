<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'group_name',
        'description',
        'visibility_public',
        'owner',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }
}
