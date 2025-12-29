<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_id',
        'bank_account_number',
        'bank_name',
        'amount',
        'payment_proof_url',
        'verification_status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
