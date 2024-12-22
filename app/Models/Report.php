<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_reports';

    protected $primaryKey = 'reportid';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $fillable = [
        'reason',
        'userid',
        'commentid',
        'postid',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'userid');
    }
}
