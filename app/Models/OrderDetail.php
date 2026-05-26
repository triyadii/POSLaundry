<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'service_id',
        'qty',
        'price',
        'subtotal',
        'notes',
        'status',
        'item_condition',
        'photo_path'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
