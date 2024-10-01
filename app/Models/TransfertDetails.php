<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransfertDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfert_id',         // Foreign key to proforma
        'item_id',         // Foreign key to Item
        'unit_id',         // Foreign key to Unit
        'quantity',        // Quantity of the item
        'total',           // Total amount for this line (quantity * price)
    ];

    // Relationship with the Cash model
    public function godownshop()
    {
        return $this->belongsTo(Transfert::class, 'transfert_id');
    }


    // Relationship with the Item model
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relationship with the Unit model
    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }


}
