<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableType extends Model
{
    protected $fillable = ['type_name', 'description'];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
