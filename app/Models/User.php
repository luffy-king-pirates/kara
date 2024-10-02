<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'profile_picture',
        'created_at',
        'updated_at',
        'is_deleted',
        'last_login',  // Add this
        'last_logout', // Add this
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        // Parse the timestamps
        $lastLogin = Carbon::parse($this->last_login);
        $lastLogout = Carbon::parse($this->last_logout);
        $now = Carbon::now('UTC'); // Get the current time

    
        // Evaluate conditions
        $isLastLoginPast = $lastLogin < $now; // Check if last_login is before now
        $isLastLogoutFuture = $lastLogout > $now; // Check if last_logout is after now


        // Check the final login status
        // The user is logged in if last_login is in the past and last_logout is NOT in the future
        $isLoggedIn = $this->last_login && $this->last_logout && $isLastLoginPast && !$isLastLogoutFuture
        && $lastLogout<$lastLogin
        ;
        Log::debug('Login Status: ' . ($isLoggedIn ? 'true' : 'false'));

        return $isLoggedIn;

         }
    public function adminlte_image()
    {
        return $this->profile_picture ? 'storage/' . $this->profile_picture : 'https://res.cloudinary.com/dwzht4utm/image/upload/v1727019534/images_b5ws3b.jpg'; // Adjust the path as needed
    }

    // Define the relationship with roles through the UserAssignRole pivot table
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_assign_role', 'user_id', 'role_id');
    }

    public function hasRole($role)
{
    // Check if the user has a specific role
    return $this->roles()->where('name', $role)->exists();
}
public function getRoleNames(): string
{
    return $this->roles->pluck('role_name')->implode(', ');
}

}
