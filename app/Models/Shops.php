<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shops extends Model
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

            // Fetch stock type based on stock_type_id from stock_type table
            $stockType = StockTypes::find($detail->stock_type_id);

            if ($stockType) {
                if ($stockType->stock_type_name == 'increase') {
                    // Increase quantity
                    if ($godown) {
                        $godown->quantity += $detail->quantity;
                        $godown->save();
                    } else {
                        // Create new entry if item doesn't exist
                        $godown = self::create([
                            'item_id' => $detail->item_id,
                            'unit_id' => $detail->unit_id,
                            'quantity' => $detail->quantity,
                        ]);
                    }
                } elseif ($stockType->stock_type_name == 'decrease') {
                    // Decrease quantity
                    if ($godown) {
                        $godown->quantity -= $detail->quantity;
                        if ($godown->quantity <= 0) {
                            $godown->delete();
                        } else {
                            $godown->save();
                        }
                    }
                }
            } else {
                // Default behavior: increase stock if stock_type_id is not specified
                if ($godown) {
                    $godown->quantity += $detail->quantity;
                    $godown->save();
                } else {
                    $godown = self::create([
                        'item_id' => $detail->item_id,
                        'unit_id' => $detail->unit_id,
                        'quantity' => $detail->quantity,
                    ]);
                }
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
                // Fetch stock type based on stock_type_id from stock_type table
                $stockType = StockTypes::find($detail->stock_type_id);

                if ($stockType) {
                    if ($stockType->stock_type_name == 'decrease') {
                        $godown->quantity -= $detail->quantity;
                    } elseif ($stockType->stock_type_name == 'increase') {
                        $godown->quantity += $detail->quantity;
                    }
                } else {
                    // Default behavior: decrease stock if stock_type_id is not specified
                    $godown->quantity -= $detail->quantity;
                }

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
