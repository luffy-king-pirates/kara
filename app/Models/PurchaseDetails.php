<?php

// app/Models/PurchaseDetail.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    protected $fillable = [
        'purchase_id',
        'item_id',
        'quantity',
        'cost',
        'total',
        'unit_id',
        'currency_id'
    ];

    // Relationship to the purchase
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Relationship to the item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship to the unit
    public function unit()
    {
        return $this->belongsTo(Units::class);
    }

    // Relationship to the currency
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
