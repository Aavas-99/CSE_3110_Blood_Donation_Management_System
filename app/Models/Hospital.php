<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Hospital extends Authenticatable
{
    // Primary key override
    protected $primaryKey = 'hospital_id';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'district',
        'address',
        'website',
    ];

    // Hide from JSON output
    protected $hidden = [
        'password',
    ];

    // Auto-hash password when set
    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}