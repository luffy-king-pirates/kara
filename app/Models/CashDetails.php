<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_id',         // Foreign key to Cash
        'stock_type_id',   // Foreign key to StockType
        'item_id',         // Foreign key to Item
        'unit_id',         // Foreign key to Unit
        'quantity',        // Quantity of the item
        'price',           // Price per unit
        'total',           // Total amount for this line (quantity * price)
    ];

    // Relationship with the Cash model
    public function cash()
    {
        return $this->belongsTo(Cash::class, 'cash_id');
    }

    // Relationship with the StockType model
    public function stockType()
    {
        return $this->belongsTo(StockTypes::class, 'stock_type_id');
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

    // Access the customer through the Cash model
    public function customer()
    {
        return $this->cash->customer;  // This pulls the customer associated with the Cash transaction
    }
}
