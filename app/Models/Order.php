<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'invoice_no',
        'table_id',
        'customer_id',
        'staff_production_id',
        'customer_name',
        'customer_email',
        'delivery_address',
        'order_type',
        'subtotal',
        'tax',
        'delivery_fee',
        'total_weight_qty',
        'grand_total',
        'payment_method',
        'payment_status',
        'dp_amount',
        'down_payment',
        'order_status',
        'snap_token',
        'promo_id',
        'discount_amount',
        'cash_received',
        'cash_change',
        'special_instructions',
        'estimated_completed_at',
        'actual_completed_at',
        'picked_up_delivered_at'
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function staffProduction()
    {
        return $this->belongsTo(User::class, 'staff_production_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'asc');
    }

    public function pickupsDeliveries()
    {
        return $this->hasMany(PickupDelivery::class);
    }

    // Accessor to map virtual down_payment to dp_amount database column
    public function getDownPaymentAttribute()
    {
        return $this->dp_amount ?? 0;
    }

    // Mutator to map virtual down_payment to dp_amount database column
    public function setDownPaymentAttribute($value)
    {
        $this->attributes['dp_amount'] = $value;
    }
}
