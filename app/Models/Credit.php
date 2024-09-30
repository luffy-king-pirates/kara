<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_number',      // The identifier for the cash transaction
        'creation_date',
        'total_amount',     // Total value of the cash transaction
        'is_deleted',
        'is_active',
        'created_by',
        'updated_by',
        'customer_id',      // Foreign key for Customer
        'created_at',
        'updated_at',
    ];

    // Relationship with the User who created the cash entry
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with the User who updated the cash entry
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relationship with the Customer model
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }



    // Relationship with CashDetail
    public function details()
    {
        return $this->hasMany(CreditDetails::class);
    }
}
