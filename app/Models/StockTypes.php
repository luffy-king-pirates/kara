<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_type_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_by'
    ];
   // Relationship for the user who created the unit
public function createdByUser() {
    return $this->belongsTo(User::class, 'created_by');
}

// Relationship for the user who updated the unit
public function updatedByUser() {
    return $this->belongsTo(User::class, 'updated_by');
}
}
