<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $table = 'pickuplist';
    protected $primaryKey = 'pickup_id';

    protected $fillable = [
        'cu_name',
        'comments',
        'route_id',
        'entry_date',
        'pickup_date',
        'op_id',
        'complete',
        'complete_date',
        'visible',
        'remove_op_id',
        'remove_date',
        'seqno',
        'pickup_seqno',
        'notification'
    ];
}
