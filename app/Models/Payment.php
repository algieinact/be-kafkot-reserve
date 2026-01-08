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

    // Accessor to return full URL for payment proof
    public function getPaymentProofUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If already a full URL, return as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Otherwise, prepend storage URL
        return asset('storage/' . $value);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
