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

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Register the Post model with the PostPolicy
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
        Group::class => GroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register the policies
        $this->registerPolicies();

        // Optional: Define additional Gates if needed
    }
}
