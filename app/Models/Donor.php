<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Donor extends Authenticatable
{
    protected $primaryKey = 'donor_id';

    protected $fillable = [
        'name', 'email', 'password', 'blood_group', 'gender', 'phone',
        'address', 'district', 'date_of_birth', 'last_donated_at',
        'status', 'hospital_id'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_donated_at' => 'datetime',
    ];

    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'hospital_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id', 'donor_id');
    }

    // Check if donor is eligible (age >= 18 and last donation > 90 days ago)
    public function isEligible(): bool
    {
        $age = \Carbon\Carbon::parse($this->date_of_birth)->age;
        if ($age < 18) return false;

        if ($this->last_donated_at) {
            $daysSinceLast = $this->last_donated_at->diffInDays(now());
            if ($daysSinceLast < 90) return false;
        }

        return $this->status === 'available';
    }

    public function getEligibilityText(): string
    {
        return $this->isEligible() ? 'Eligible' : 'Not Eligible';
    }

    public function getEligibilityClass(): string
    {
        return $this->isEligible() ? 'eligible' : 'not-eligible';
    }
}
