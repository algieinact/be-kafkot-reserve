<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'table_id',
        'reservation_date',
        'reservation_time',
        'number_of_people',
        'total_amount',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}