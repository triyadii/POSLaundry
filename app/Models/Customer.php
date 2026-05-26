<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'member_status',
        'loyalty_points',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function pickupsDeliveries()
    {
        return $this->hasMany(PickupDelivery::class);
    }
}
