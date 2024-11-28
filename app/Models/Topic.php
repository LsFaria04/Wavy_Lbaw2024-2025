<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'topic';

    protected $primaryKey = 'topicid';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $fillable = [
        'topicname'
    ];
    
}
