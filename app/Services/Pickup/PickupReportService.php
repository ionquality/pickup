<?php

namespace App\Services\Pickup;

use App\Helpers\Helpers;
use App\Models\AutomaticPickup;
use App\Models\Pickup;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function buildAllPickupList(Request $request){
        $routes = [2,3,4,5,6,8];
        $route_id = $request->input('route_id');
        $startDate = $request->input('startDate') ?? Carbon::now()->subDays(90)->toDateString();
        $endDate = $request->input('endDate') ?? Carbon::now()->toDateString();
        $complete = $request->input('complete');
        $delete = $request->input('delete');
        $completeCheck = $complete == 1 ? 'checked' : '';
        $deleteCheck = $delete == 1 ? 'checked' : '';
        $allPickups = Pickup::allPickups($route_id, $startDate, $endDate, $complete, $delete)->orderBy('entry_date','desc')->get();

        $html = '<h3>Pickups Report</h3>';
        $html .= '<form id="filterReport">';

        $html .= '<div class="table-responsive"><table class="table">';
        $html .= '<tr class="table-info"><th>Route</th><th>Start Date</th><th>End Date</th><th>Completed</th><th>Deleted</th><th>Filter</th></tr>';
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
        $html .= '<div class="form-check form-check-primary"><input class="form-check-input" name="complete" type="checkbox" value="1" '.$completeCheck.'  /></div>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<div class="form-check form-check-primary"><input class="form-check-input" name="delete" type="checkbox" value="1" '.$deleteCheck.'  /></div>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<input type="hidden" name="type" value="All">';
        $html .= '<button type="button" class="btn btn-success" onclick="filterReport()"><i class="fa fa-check"></i></button>';
        $html .= '</form>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table></div>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table table-sm">';
        $html .= '<thead><tr class="table-primary table-sm">';
        $html .= '<th>Route</th><th>Customer</th><th>City</th><th>Comments</th><th>Date Added</th><th>Complete</th><th>Deleted</th></tr></thead>';
        foreach ($allPickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.$pickup->cu_city.'</td><td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->entry_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->complete_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->remove_date).'</td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        return $html;
    }

    public function buildAutomaticPickupList () {
        $automaticPickups = AutomaticPickup::orderBy('cu_name')->get();

        $html = '<h3>Automatic Pickups</h3>';
        $html .= '<button class="btn btn-primary" onclick="addAutomaticPickupForm()">Add Automatic Pickup</button>';
        $html .= '<button class="btn btn-info ms-3" onclick="addAutomaticPickups()">Add Pickups To Today\'s List</button>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table table-sm">';
        $html .= '<thead><tr class="table-primary table-sm">';
        $html .= '<th>Route</th><th>Customer</th><th>Delete</th></tr></thead>';
        foreach ($automaticPickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td><button class="btn btn-sm btn-danger" onclick="deleteAutomaticPickup('.$pickup->id.')"><i class="fa fa-trash"></i></button></td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        return $html;
    }

    public function addAutomaticPickupsToPickupList (){
        $automaticPickups = AutomaticPickup::orderBy('cu_name')->get();

        foreach ($automaticPickups as $pickup){
            $count = Pickup::count();
            $count ++;
            $pickup_seq = Pickup::join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
                ->where('CUSTOMER.cu_active','Y')
                ->where('pickuplist.complete','N')
                ->where('pickuplist.visible','Y')
                ->where('pickuplist.route_id', $pickup->route_id)
                ->count();
            $pickup_seq++;
            Pickup::create([
                'cu_name' => $pickup->cu_name,
                'comments' => 'Automatic Pickup',
                'route_id' => $pickup->route_id,
                'entry_date' => now(),
                'pickup_date' => Carbon::now()->toDateString(),
                'op_id' => '102612',
                'complete' => 'N',
                'visible' => 'Y',
                'seqno' => $count,
                'pickup_seqno' => $pickup_seq,
                'notification' => 'N'
            ]);
        }
    }



}
