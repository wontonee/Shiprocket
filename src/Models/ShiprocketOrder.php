<?php

namespace Wontonee\Shiprocket\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Sales\Models\Order;

class ShiprocketOrder extends Model
{
    protected $table = 'shiprocket_orders';

    protected $fillable = [
        'order_id',
        'shiprocket_order_id',
        'shiprocket_shipment_id',
        'status',
        'tracking_number',
        'courier_name',
        'awb_code'
    ];

    /**
     * Get the order that belongs to the shipment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}