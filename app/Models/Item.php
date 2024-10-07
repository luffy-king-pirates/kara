<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Item extends Model
{
    use HasFactory;

    // Table name (optional if it matches the plural form of the model name)
    protected $table = 'items';

    // Mass assignable attributes
    protected $fillable = [
        'id',
        'item_code',
        'item_name',
        'item_category',
        'item_brand',
        'item_size',
        'created_by',
        'updated_by',
        'is_active',
        'is_deleted',
        'created_at',
        'updated_at',
        'item_unit'
    ];

    // Relationships

    // Relation to ItemCategory (assuming there's an ItemGroup model)
    public function category()
    {
        return $this->belongsTo(Categories::class, 'item_category');
    }

    // Relation to Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'item_brand');
    }

    public function unit()
    {
        return $this->belongsTo(Units::class, 'item_unit');
    }

           // Relationship for the user who created the unit
public function createdByUser() {
    return $this->belongsTo(User::class, 'created_by');
}

// Relationship for the user who updated the unit
public function updatedByUser() {
    return $this->belongsTo(User::class, 'updated_by');
}

    // Scope to get only active items
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get only non-deleted items
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }


    public function godown()
    {
        return $this->hasOne(Godown::class, 'item_id');
    }

    public function shops()
    {
        return $this->hasOne(Shops::class, 'item_id');
    }

    public function shopAshaks()
    {
        return $this->hasOne(ShopAshaks::class, 'item_id');
    }



    public function shopService()
    {
        return $this->hasOne(ShopService::class, 'item_id');
    }




}
