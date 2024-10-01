<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfert_number',      // The identifier for the cash transaction
        'transfert_date',
        'is_deleted',
        'is_approved',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'transfert_from',
        'transfert_to',
        'time_in',
        'time_out'
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


    // Relationship with CashDetail
    public function details()
    {
        return $this->hasMany(TransfertDetails::class);
    }
}
