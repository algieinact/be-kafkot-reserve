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

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }
}
