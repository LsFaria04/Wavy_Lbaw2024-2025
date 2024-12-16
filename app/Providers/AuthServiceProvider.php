<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Post;
use App\Policies\PostPolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Group;
use App\Policies\GroupPolicy;
use App\Models\Topic;
use App\Policies\TopicPolicy;
use App\Models\Follow;
use App\Policies\FollowPolicy;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
        Group::class => GroupPolicy::class,
        Topic::class => TopicPolicy::class,
        Follow::class => FollowPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {
        $this->registerPolicies();

        // Optional: Define additional Gates if needed
    }
}
