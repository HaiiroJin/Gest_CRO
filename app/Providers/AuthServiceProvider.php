<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-attestations', function (User $user) {
            return $user->hasRole('super_admin');
        });

        Gate::define('print-attestation', function (User $user) {
            return $user->hasRole('super_admin');
        });

        Gate::define('manage-conges', function (User $user) {
            return $user->hasRole('super_admin');
        });
    }
} 