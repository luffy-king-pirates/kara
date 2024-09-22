<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssignRole extends Model
{
    use HasFactory;

    protected $table = 'user_assign_role'; // Pivot table name

    protected $fillable = [
        'user_id',
        'role_id',
        'created_at',
        'updated_at',
      
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
