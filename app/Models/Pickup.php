<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $table = 'pickuplist';
    protected $primaryKey = 'pickup_id';
    public $timestamps = false;

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

    public function scopeOpenPickups($query,$route_id = null)
    {
        $query->join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
            ->select('CUSTOMER.cu_city','pickuplist.*')
            ->where('CUSTOMER.cu_active','Y');
        if ($route_id){
            $query->where('pickuplist.route_id',$route_id);
        }
        $query->where('pickuplist.complete','N')
            ->where('pickuplist.visible','Y');
        return $query;
    }

    public function scopeCompletePickupsToday($query,$route_id = null)
    {
        $query->join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
            ->select('CUSTOMER.cu_city','pickuplist.*')
            ->where('CUSTOMER.cu_active','Y');
        if ($route_id){
            $query->where('pickuplist.route_id',$route_id);
        }
        $query->whereDate('complete_date', Carbon::today());
        $query->where('pickuplist.complete','Y')
            ->where('pickuplist.visible','Y');
        return $query;
    }

    public function scopeDeletedPickups($query,$route_id = null, $start_date, $end_date)
    {
        $query->join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
            ->select('CUSTOMER.cu_city','pickuplist.*')
            ->where('CUSTOMER.cu_active','Y');
        if ($route_id){
            $query->where('pickuplist.route_id',$route_id);
        }
        $query->whereDate('pickuplist.remove_date', '>=', $start_date);
        $query->whereDate('pickuplist.remove_date', '<=', $end_date);

        $query->where('pickuplist.visible','N');
        return $query;
    }
}
