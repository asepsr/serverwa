<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_number',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'payment_proof_url',
        'shipping_address',
        'notes',
        'ordered_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function orderStatuses()
    {
        return $this->hasMany(OrderStatus::class);
    }
}
