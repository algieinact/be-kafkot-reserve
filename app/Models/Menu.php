<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'menu_name',
        'description',
        'price',
        'category',
        'image_url',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }
}
