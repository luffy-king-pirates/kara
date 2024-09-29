<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_id',
        'item_id',
        'stock_type_id',
        'godown',
        'shop',
        'quantity',
        'unit_id'
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stockType()
    {
        return $this->belongsTo(StockTypes::class);
    }

    public function unit()
    {
        return $this->belongsTo(Units::class);
    }
}

