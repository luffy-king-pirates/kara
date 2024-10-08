<?php
// app/Models/Purchase.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imports extends Model
{
    protected $fillable = [
        'import_number',
        'supplier_id',
        'created_by',
        'updated_by',
        'is_deleted',
        'is_approved'
    ];

    // Relationship to the supplier
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }

    // Relationship to the user who created the purchase
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship to the user who last updated the purchase
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relationship to the purchase details
    public function details()
    {
        return $this->hasMany(ImportDetails::class);
    }
}
