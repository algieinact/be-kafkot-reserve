<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'reservation_id',
        'payment_id',
        'recipient_email',
        'email_type',
        'subject',
        'body',
        'status',
        'error_message',
        'sent_at',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
