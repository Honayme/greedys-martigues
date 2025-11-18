<?php

namespace Webkul\MondialRelay\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Sales\Models\Order;

class OrderMondialRelay extends Model
{
    protected $table = 'order_mondial_relay';

    protected $fillable = [
        'order_id',
        'delivery_mode',
        'point_relais_id',
        'point_relais_name',
        'point_relais_address',
        'point_relais_city',
        'point_relais_postcode',
        'point_relais_country',
        'tracking_number',
        'label_url',
    ];

    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
