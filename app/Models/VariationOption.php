<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationOption extends Model
{
    protected $fillable = [
        'variation_group_id',
        'name',
        'price_adjustment',
        'is_default',
        'order',
    ];

    protected $casts = [
        'variation_group_id' => 'integer',
        'price_adjustment' => 'decimal:2',
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the variation group that owns this option
     */
    public function variationGroup()
    {
        return $this->belongsTo(VariationGroup::class);
    }

    /**
     * Get formatted price adjustment
     */
    public function getFormattedPriceAdjustmentAttribute()
    {
        if ($this->price_adjustment == 0) {
            return null;
        }

        return $this->price_adjustment > 0
            ? '+Rp ' . number_format($this->price_adjustment, 0, ',', '.')
            : '-Rp ' . number_format(abs($this->price_adjustment), 0, ',', '.');
    }
}
