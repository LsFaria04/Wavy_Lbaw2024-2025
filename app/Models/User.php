<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    public $primaryKey = 'userid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'passwordhash',
        'bio',
        'state',
        'visibilitypublic',
        'isadmin',
        'search',
        'state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'passwordhash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the login password to be used by the controller.
     * 
     * @var string
     * 
     **/
    public function getAuthPassword()
    {
        return $this->passwordhash;
    }

    /**
     * Get the posts for a user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'userid');
    }

        /**
     * Get the comments made by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'userid', 'userid');
    }

    /**
     * Get the likes made by the user.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'userid', 'userid');
    }

    public function getIsAdmin(): bool {
        return (bool) $this->attributes['isadmin'];
    }

    /**
     * Get the groups that the user is a part of.
     */
    public function groups(): BelongsToMany {
        return $this->belongsToMany(Group::class, 'group_membership', 'userid', 'groupid');
    }

    public function topics() {
        return $this->belongsToMany(Topic::class, 'user_topics', 'userid', 'topicid');
    }

}
