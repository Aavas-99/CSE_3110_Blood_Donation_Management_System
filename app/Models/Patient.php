<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'name', 'email', 'password', 'blood_group', 'gender', 'phone',
        'address', 'district', 'date_of_birth', 'status'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function emergencyRequests()
    {
        return $this->hasMany(EmergencyRequest::class, 'patient_id', 'patient_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'patient_id', 'patient_id');
    }
}