<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
class Permission extends Model
{
    use HasFactory;
    protected $fillable = ['action', 'page'];
     // Define the relationship with roles
     public function roles()
     {
         return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
     }
}
