<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'currencie_name',
        'currencie_value',
        'created_by',
        'updated_by',
        'created_at',
        'updated_by',
        'is_deleted'
    ];

    // Relationship with the User model for 'created_by'
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with the User model for 'updated_by'
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
