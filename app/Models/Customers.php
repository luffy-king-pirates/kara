<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_tin',
        'customer_vrn',
        'customer_location',
        'customer_address',
        'customer_mobile',
        'customer_email',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // Define the relationship to the User who created the customer
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Define the relationship to the User who updated the customer
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Example of a method to get the full address
    public function getFullAddressAttribute()
    {
        return "{$this->customer_address}, {$this->customer_location}";
    }
}
