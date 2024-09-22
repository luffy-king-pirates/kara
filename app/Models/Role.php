<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_by',
    ];

    // Relationship for the user who created the role
    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship for the user who updated the role
    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Define the relationship with users through the UserAssignRole pivot table
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_assign_role', 'role_id', 'user_id');
    }
}
