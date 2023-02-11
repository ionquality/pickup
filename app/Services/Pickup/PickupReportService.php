<?php

namespace App\Services\Pickup;


use App\Helpers\Helpers;
use App\Models\Customer;
use App\Models\Pickup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Pickups
 *
 * This service handles the display of Pickups
 */
class PickupReportService
{
    /**
     * Build List of deleted pickups
     *
     * @group Pickups
     * @return string
     */
    public function buildDeletedPickupList(Request $request){
        $routes = [2,3,4,5,6,8];
        $route_id = $request->input('route_id');
        $startDate = $request->input('startDate') ?? Carbon::now()->subYear()->toDateString();
        $endDate = $request->input('endDate') ?? Carbon::now()->toDateString();

        $deletedPickups = Pickup::deletedPickups($route_id, $startDate, $endDate)->orderBy('remove_date','desc')->get();

        $html = '<h3>Deleted Pickups Report</h3>';
        $html .= '<form id="filterReport">';

        $html .= '<div class="table-responsive"><table class="table">';
        $html .= '<tr class="table-info"><th>Route</th><th>Start Date</th><th>End Date</th><th>Filter</th></tr>';
        $html .= '<tr>';
        $html .= '<td><select class="form-control" name="route_id">';
        if ($route_id){
            $html .= '<option>'.$route_id.'</option>';
        }
        $html .= '<option value="">All</option>';
        foreach ($routes as $route){
            $html .= '<option>'.$route.'</option>';
        }
        $html .= '</select></td>';
        $html .= '<td><input type="date" class="form-control" name="startDate" value="'.$startDate.'"></td>';
        $html .= '<td><input type="date" class="form-control" name="endDate" value="'.$endDate.'"></td>';
        $html .= '<td>';
        $html .= '<input type="hidden" name="type" value="Deleted">';
        $html .= '<button type="button" class="btn btn-success" onclick="filterReport()"><i class="fa fa-check"></i></button>';
        $html .= '</form>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table></div>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table table-sm">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>City</th><th>Comments</th><th>Date Added</th><th>Date Deleted</th><th>Deleted By</th></tr></thead>';
        foreach ($deletedPickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.$pickup->cu_city.'</td><td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->entry_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->remove_date).'</td>';
            $html .= '<td>'.Helpers::getUserName($pickup->remove_op_id).'</td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        return $html;
    }




}
