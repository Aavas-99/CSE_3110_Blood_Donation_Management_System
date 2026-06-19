<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    protected $primaryKey = 'req_id';
    public $timestamps = true;

    protected $fillable = [
        'patient_id', 'blood_group', 'quantity_units', 'message',
        'urgency_level', 'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'req_id', 'req_id');
    }

    public function getUrgencyColor(): string
    {
        return match($this->urgency_level) {
            'critical' => 'bg-red-950/60 text-red-400 border-red-900',
            'urgent' => 'bg-orange-950/60 text-orange-400 border-orange-900',
            default => 'bg-green-950/60 text-green-400 border-green-900',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'completed' => 'bg-green-950/60 text-green-400 border-green-900',
            'approved' => 'bg-blue-950/60 text-blue-400 border-blue-900',
            'rejected' => 'bg-red-950/60 text-red-400 border-red-900',
            'cancelled' => 'bg-gray-800 text-gray-400 border-gray-700',
            default => 'bg-yellow-950/60 text-yellow-400 border-yellow-900',
        };
    }

    public function getUrgencySort(): int
    {
        return match($this->urgency_level) {
            'critical' => 3,
            'urgent' => 2,
            default => 1,
        };
    }
}