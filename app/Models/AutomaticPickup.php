<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AutomaticPickup extends Model
{
    protected $table = 'AUTO_PICKUPLIST';

    protected $fillable = [
        'customer_id',
        'cu_name',
        'route_id',
    ];

}
