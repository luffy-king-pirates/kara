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




        // Define all entities for which we want to set permissions
        $entities = [
            'brand', 'category', 'country', 'currency', 'customer','items',
            'month', 'role', 'stock-type', 'supplier', 'unit', 'user', 'year', 'user-assigned-role',
            'godwan-to-shop' ,
        'godwan-to-shop-ashok' ,
        'shop-to-godwan' ,
        'shop-service-to-godwan',
        'existance-transfert',
        'cash-sale',
        'credit-sale' ,
        'dashboard' ,
        'dashboard-sales',
        'local-purchase' ,
        'imports' ,
        'adjustments',

        ];

        // Loop through each entity and define gates for CRUD operations
        foreach ($entities as $entity) {
            // Capitalize entity name for page matching (e.g. 'Brands', 'Categories', etc.)
            $entityCapitalized = ucfirst($entity);

            // Create Gate for Create operation
            Gate::define("create-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'create')->where('page', $entityCapitalized);
                })->exists();
            });

            // Create Gate for Read operation
            Gate::define("read-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'read')->where('page', $entityCapitalized);
                })->exists();
            });

            // Create Gate for Update operation
            Gate::define("update-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'update')->where('page', $entityCapitalized);
                })->exists();
            });

            // Create Gate for Delete operation
            Gate::define("delete-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'delete')->where('page', $entityCapitalized);
                })->exists();
            });

            // Create Gate for export operation
            Gate::define("export-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'export')->where('page', $entityCapitalized);
                })->exists();
            });

              // Create Gate for manage operation
              Gate::define("manage-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'manage')->where('page', $entityCapitalized);
                })->exists();
            });
            // Create Gate for manage operation
            Gate::define("export-details-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'export-details')->where('page', $entityCapitalized);
                })->exists();
            });
            // Create Gate for manage operation
            Gate::define("export-pdf-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'export-pdf')->where('page', $entityCapitalized);
                })->exists();
            });
            // Create Gate for manage operation
            Gate::define("approve-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'approve')->where('page', $entityCapitalized);
                })->exists();
            });

            Gate::define("export-pdf-with-header-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'export-pdf-with-header')->where('page', $entityCapitalized);
                })->exists();
            });

            Gate::define("export-pdf-without-header-$entity", function (User $user) use ($entityCapitalized) {
                return $user->roles()->whereHas('permissions', function($query) use ($entityCapitalized) {
                    $query->where('action', 'export-pdf-without-header-header')->where('page', $entityCapitalized);
                })->exists();
            });

        }

    }
}


