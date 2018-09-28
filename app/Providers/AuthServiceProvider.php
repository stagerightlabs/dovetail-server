<?php

namespace App\Providers;

use App\Page;
use App\Comment;
use App\Notebook;
use App\Invitation;
use App\Organization;
use App\Policies\CommentPolicy;
use App\Policies\InvitationPolicy;
use App\Policies\OrganizationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Comment::class => CommentPolicy::class,
        Invitation::class => InvitationPolicy::class,
        Organization::class => OrganizationPolicy::class,
     ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super admins will be allowed access to all gates
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Check that the current user has been granted a specific permission
        Gate::define('require-permission', function ($user, $permission) {
            return $user->hasPermission($permission);
        });

        // Verify that the current user "owns" a model
        Gate::define('ownership-verification', function ($user, $model, $column = 'owner_id') {
            return $user->id == $model->$column;
        });

        // Verify that a requested model lives within the scope of a users organization
        Gate::define('belongs-to-organization', function ($user, $model) {
            if (array_key_exists('organization_id', $model->attributesToArray())) {
                return request()->organization()->id == $model->organization_id;
            }

            if ($model instanceof Page) {
                return request()->organization()->id == $model->notebook->organization_id;
            }
        });
    }
}
