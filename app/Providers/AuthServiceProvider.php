<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
    // public function boot(): void
    // {
    //     //
    // }
//     public function boot()
// {
//     $this->registerPolicies();

//     Gate::define('view-installment-item', function ($user) {
//         // Define your authorization logic here. For example, check if the user has a specific role or permission to view the page.
//         return $user->role->name === 'Customer' || $user->can('view-installment-item');
//     });
// }
}
