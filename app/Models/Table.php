<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $table = 'tables';

    protected $fillable = ['table_type_id', 'table_number', 'capacity', 'status', 'floor', 'position_x', 'position_y', 'orientation', 'span_x', 'span_y'];

    public function tableType()
    {
        return $this->belongsTo(TableType::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    protected $casts = [
        'capacity' => 'integer',
        'floor' => 'integer',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'table_type_id' => 'integer',
        'span_x' => 'integer',
        'span_y' => 'integer',
    ];
}
