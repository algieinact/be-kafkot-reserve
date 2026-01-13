<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationGroup extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_required',
        'min_selections',
        'max_selections',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'min_selections' => 'integer',
        'max_selections' => 'integer',
    ];

    /**
     * Get the options for this variation group
     */
    public function options()
    {
        return $this->hasMany(VariationOption::class)->orderBy('order');
    }

    /**
     * Get the menus that have this variation group
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_variations');
    }

    /**
     * Scope for required variation groups
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for optional variation groups
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }
}
