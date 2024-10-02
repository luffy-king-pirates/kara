<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Godown extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',  // Foreign key to the Item
        'unit_id',  // Foreign key to the Unit
        'quantity', // Quantity available in the godown
    ];

    // Relationship with the Item model
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relationship with the Unit model
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Add items to the godown based on transfert details.
     */
    public static function addItemsFromTransfert($transfert)
    {
        foreach ($transfert->details as $detail) {
            $godown = self::where('item_id', $detail->item_id)
                ->where('unit_id', $detail->unit_id)
                ->first();

            if ($godown) {
                // If the item already exists, increase its quantity
                $godown->quantity += $detail->quantity;
                $godown->save(); // Save the updated quantity
            } else {
                // Otherwise, create a new godown entry
                $godown = self::create([
                    'item_id' => $detail->item_id,
                    'unit_id' => $detail->unit_id,
                    'quantity' => $detail->quantity,
                ]);
            }
        }
    }

    public static function removeItemsFromTransfert($transfert)
{
    foreach ($transfert->details as $detail) {
        $godown = self::where('item_id', $detail->item_id)
            ->where('unit_id', $detail->unit_id)
            ->first();

        if ($godown) {
            // Decrease the quantity of the item in godown
            $godown->quantity -= $detail->quantity;

            // If the quantity falls below or equals 0, delete the entry
            if ($godown->quantity <= 0) {
                $godown->delete();
            } else {
                $godown->save(); // Save the updated quantity
            }
        }
    }
}

}
