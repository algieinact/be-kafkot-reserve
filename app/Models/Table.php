<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $table = 'tables';

    protected $fillable = ['table_type_id', 'table_number', 'capacity', 'status'];

    public function tableType()
    {
        return $this->belongsTo(TableType::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
