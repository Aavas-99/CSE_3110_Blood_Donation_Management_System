<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $primaryKey = 'donation_id';

    protected $fillable = [
        'donor_id', 'hospital_id', 'date', 'quantity_units',
        'status', 'patient_id', 'req_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id', 'donor_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'hospital_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function request()
    {
        return $this->belongsTo(EmergencyRequest::class, 'req_id', 'req_id');
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'completed' => 'bg-green-950/60 text-green-400 border-green-900',
            'scheduled' => 'bg-blue-950/60 text-blue-400 border-blue-900',
            'cancelled' => 'bg-red-950/60 text-red-400 border-red-900',
            default => 'bg-gray-800 text-gray-400 border-gray-700',
        };
    }
}