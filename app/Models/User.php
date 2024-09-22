<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'updated_at'
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

    public function adminlte_image()
    {
        return $this->profile_picture ? 'storage/' . $this->profile_picture : 'https://res.cloudinary.com/dwzht4utm/image/upload/v1727019534/images_b5ws3b.jpg'; // Adjust the path as needed
    }

    // Define the relationship with roles through the UserAssignRole pivot table
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_assign_role', 'user_id', 'role_id');
    }
}
