<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all menus for this category
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get menus count attribute
     */
    public function getMenusCountAttribute()
    {
        return $this->menus()->count();
    }
}
