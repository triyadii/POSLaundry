<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupDelivery extends Model
{
    protected $table = 'pickups_deliveries';

    protected $fillable = [
        'order_id',
        'courier_id',
        'type',
        'status',
        'address',
        'latitude',
        'longitude',
        'fee',
        'proof_photo_path',
        'notes',
        'completed_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }
}
