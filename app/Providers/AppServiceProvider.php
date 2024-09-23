<?php
namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define role-based permission gates
        Gate::define('admin', function (User $user) {
            // Check if the user has the 'Admin' role (adjust as needed)
            return $user->roles()->where('role_name', 'admin')->exists();
        });

        // You can define more gates for other permissions here
    }
}
