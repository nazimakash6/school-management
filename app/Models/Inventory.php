<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventories';

    protected $guarded = [];

    protected $casts = [
        'quantity'           => 'integer',
        'min_quantity_alert' => 'integer',
        'unit_price'         => 'float',
        'total_cost'         => 'float',
        'purchase_date'      => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::saving(function (Inventory $item) {
            // Auto generate Item Code if empty
            if (empty($item->item_code)) {
                $prefix = match($item->category) {
                    'Electronics & IT' => 'INV-IT',
                    'Furniture'        => 'INV-FUR',
                    'Lab Equipment'    => 'INV-LAB',
                    'Stationery'       => 'INV-ST',
                    'Sports Goods'     => 'INV-SPT',
                    default            => 'INV-GEN',
                };
                $item->item_code = $prefix . '-' . rand(100, 999) . '-' . strtoupper(Str::random(3));
            }

            // Auto calculate Total Cost
            if ($item->quantity >= 0 && $item->unit_price >= 0) {
                $item->total_cost = (float)$item->quantity * (float)$item->unit_price;
            }

            // Auto status check for low / out of stock
            if ($item->quantity == 0) {
                $item->status = 'Out of Stock';
            } elseif ($item->quantity <= $item->min_quantity_alert && $item->status !== 'Maintenance') {
                $item->status = 'Low Stock';
            }
        });
    }

    public function getImageProofUrlAttribute(): string
    {
        if ($this->image_proof) {
            if (str_starts_with($this->image_proof, 'http://') || str_starts_with($this->image_proof, 'https://')) {
                return $this->image_proof;
            }
            return asset('storage/' . $this->image_proof);
        }
        
        // Fallback SVG data placeholder image for proof preview
        return "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=500&auto=format&fit=crop&q=60";
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            'Electronics & IT' => 'bg-primary-subtle text-primary border-primary-subtle',
            'Furniture'        => 'bg-purple-subtle text-purple border-purple-subtle',
            'Lab Equipment'    => 'bg-info-subtle text-info border-info-subtle',
            'Stationery'       => 'bg-warning-subtle text-warning border-warning-subtle',
            'Sports Goods'     => 'bg-success-subtle text-success border-success-subtle',
            default            => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Available'    => 'bg-success bg-opacity-10 text-success border border-success-subtle',
            'In Use'       => 'bg-primary bg-opacity-10 text-primary border border-primary-subtle',
            'Low Stock'    => 'bg-warning bg-opacity-10 text-warning border border-warning-subtle',
            'Out of Stock' => 'bg-danger bg-opacity-10 text-danger border border-danger-subtle',
            'Maintenance'  => 'bg-info bg-opacity-10 text-info border border-info-subtle',
            default        => 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle',
        };
    }

    public function getConditionBadgeClassAttribute(): string
    {
        return match ($this->item_condition) {
            'New / Excellent', 'Excellent', 'Good' => 'bg-success bg-opacity-10 text-success',
            'Fair'                               => 'bg-info bg-opacity-10 text-info',
            'Damaged / Repair Needed', 'Damaged'  => 'bg-danger bg-opacity-10 text-danger',
            'Disposed'                           => 'bg-secondary bg-opacity-10 text-secondary',
            default                              => 'bg-light text-dark',
        };
    }
}
