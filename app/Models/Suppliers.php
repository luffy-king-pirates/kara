<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_name',
        'supplier_location',
        'supplier_contact',
        'supplier_reference',
        'created_by',
        'updated_by',
        'is_deleted'
    ];

    // Relationship for the user who created the supplier
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship for the user who updated the supplier
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
