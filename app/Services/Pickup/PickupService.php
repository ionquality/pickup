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
class PickupService
{
    /**
     * Build List of pickups
     *
     * @group Pickups
     * @return string
     */
    public function buildPickupList(){

        $pickups = Pickup::openPickups()->get();
        $completedPickups = Pickup::completePickupsToday()->get();

        $html = '<h3>Open Pickups</h3>';
        $html .= '<button class="btn btn-primary mb-2" type="button" onclick="createPickupForm()">Add Pickup</button>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table table-sm">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>City</th><th>Comments</th><th>Pickup Date</th><th>Date Added</th><th>Delete</th><th>Complete</th></tr></thead>';
        foreach ($pickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.$pickup->cu_city.'</td><td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateString($pickup->pickup_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->entry_date).'</td>';
            $html .= '<td><button class="btn btn-danger btn-sm" onclick="deletePickup('.$pickup->pickup_id.')"><i class="fa fa-trash"></i></button></td>';
            $html .= '<td><button class="btn btn-success btn-sm" onclick="completePickup('.$pickup->pickup_id.')"><i class="fa fa-check"></i></button></td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        $html .= '<p class="text-primary mt-4"><b>Completed Pickups Today</b></p>';
        $html .= '<div class="table-responsive"><table  class="table table-sm">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>Comments</th><th>Complete Date</th><th>Date Added</th></tr></thead>';
        foreach ($completedPickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->complete_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->entry_date).'</td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        return $html;
    }

    /**
     * Build List of pickups for the driver
     *
     * @group Pickup
     * @return string
     */
    public function buildPickupDriverList($route_id){

        $pickups = Pickup::openPickups($route_id)->get();
        $pickupNew = Pickup::openPickups($route_id)->where('pickuplist.notification','Y')->exists();
        $completedPickups = Pickup::completePickupsToday($route_id)->get();

        $html = '<h3>Driver '.$route_id.': Open Pickups</h3>';
        if ($pickupNew){
            $html .= '<div class="alert alert-success" role="alert">';
            $html .= '<h3 class="text-danger">A new pickup has been added!</h3>';
            $html .= '<form id="pickupNotification">';
            $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
            $html .= '<input type="hidden" name="route_id" value="'.$route_id.'">';
            $html .= '<button type="button" class="btn btn-primary" onclick="pickupNotification()">Dismiss</button>';
            $html .= '</form>';
            $html .= '</div>';
        }
        $html .= '<div class="table-responsive"><table class="table">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>Location</th><th>Comments</th><th>Pickup Date</th><th>Date Added</th><th>Delete</th><th>Complete</th></tr></thead>';
        foreach ($pickups as $pickup){
            $pickupCustomer = Customer::select('cu_name','cu_region','cu_address_1','cu_address_2','cu_city','cu_state','cu_zip')
                ->where('cu_name',$pickup->cu_name)->first();
            $rowColor = $pickup->notification == 'Y' ? 'table-success' : '';
            $html .= '<tr class="'.$rowColor.'">';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td><a href="https://maps.google.com/maps?q='.Helpers::getCustomerAddress($pickupCustomer).'" target="_blank">'.Helpers::getCustomerAddress($pickupCustomer).'</a></td><td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateString($pickup->pickup_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->entry_date).'</td>';
            $html .= '<td><button class="btn btn-danger" onclick="deletePickup('.$pickup->pickup_id.')"><i class="fa fa-trash"></i></button></td>';
            $html .= '<td><button class="btn btn-success" onclick="completePickup('.$pickup->pickup_id.')"><i class="fa fa-check"></i></button></td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';
        $html .= '<p class="text-primary mt-4"><b>Completed Pickups Today</b></p>';
        $html .= '<div class="table-responsive"><table  class="table table-sm">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>Comments</th><th>Complete Date</th><th>Date Added</th></tr></thead>';
        foreach ($completedPickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->complete_date).'</td>';
            $html .= '<td>'.Helpers::getDateTimeString($pickup->entry_date).'</td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    /**
     * Build Pickup Create Form
     *
     * @group Pickups
     * @return string
     */
    public function buildPickupCreateForm($cu_name = null){
        $customers = Customer::select('cu_name')->where('cu_active','Y')->orderBy('cu_name')->get();
        $pickupCustomer = Customer::select('cu_name','cu_region','cu_address_1','cu_address_2','cu_city','cu_state','cu_zip')->where('cu_name',$cu_name)->first();
        $routes = ["2", "3", "4", "5", "6" , "8"];
        $html = '<h3 class="text-primary">Add Pickup</h3>';
        $html .= '<form id="createPickup">';
        $html .= '<div class="mb-2"><label>Customer</label><select class="form-control" id="customer-select" name="cu_name">';
        if ($cu_name){
            $html .= '<option>'.$cu_name.'</option>';
        } else {
            $html .= '<option value="">Select One</option>';
        }

        foreach ($customers as $customer){
            $html .= '<option>'.$customer->cu_name.'</option>';
        }
        $html .= '</select></div>';

        if ($pickupCustomer){
            $html .= '<p>Customer: '.$pickupCustomer->cu_name.'</p>';
            $html .= '<p>Address: '.Helpers::getCustomerAddress($pickupCustomer).'</p>';
            $html .= '<p>Route: '.$pickupCustomer->cu_region.'</p>';
        }
        $html .= '<div class="mb-2"><label>Route</label><select class="form-control" name="route_id">';
        if ($pickupCustomer){
            $html .= '<option>'.$pickupCustomer->cu_region.'</option>';

        } else {
            $html .= '<option value="">Select One</option>';

        }
        foreach ($routes as $route){
            $html .= '<option>'.$route.'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="mb-2"><label>Pickup Date</label><input type="date" class="form-control" name="pickup_date" value="'.Carbon::now()->toDateString().'"></div>';
        $html .= '<div class="mb-2"><label>Comments</label><textarea class="form-control" name="comments"></textarea></div>';

        $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
        $html .= '<input type="hidden" name="complete" value="N">';
        $html .= '<input type="hidden" name="visible" value="Y">';
        $html .= '<input type="hidden" name="entry_date" value="'.now().'">';
        $html .= '<input type="hidden" name="op_id" value="'.Auth::user()->op_id.'">';
        $html .= '<button type="button" class="btn btn-primary" onclick="createPickup()">Create</button>';
        $html .= '</form>';

        return $html;
    }

    /**
     * Build Automatic Pickup Create Form
     *
     * @group Pickups
     * @return string
     */
    public function buildAutomaticPickupCreateForm($cu_name = null){
        $customers = Customer::select('cu_name')->where('cu_active','Y')->orderBy('cu_name')->get();
        $pickupCustomer = Customer::select('cu_name','cu_region','cu_address_1','cu_address_2','cu_city','cu_state','cu_zip','customer_id')->where('cu_name',$cu_name)->first();
        $routes = ["2", "3", "4", "5", "6" , "8"];
        $html = '<h3 class="text-primary">Add Pickup</h3>';
        $html .= '<form id="createAutomaticPickup">';
        $html .= '<div class="mb-2"><label>Customer</label><select class="form-control" id="customer-select" name="cu_name">';
        if ($cu_name){
            $html .= '<option>'.$cu_name.'</option>';
        } else {
            $html .= '<option value="">Select One</option>';
        }

        foreach ($customers as $customer){
            $html .= '<option>'.$customer->cu_name.'</option>';
        }
        $html .= '</select></div>';

        if ($pickupCustomer){
            $html .= '<p>Customer: '.$pickupCustomer->cu_name.'</p>';
            $html .= '<p>Address: '.Helpers::getCustomerAddress($pickupCustomer).'</p>';
            $html .= '<p>Route: '.$pickupCustomer->cu_region.'</p>';
            $html .= '<input type="hidden" name="customer_id"  value="' . $pickupCustomer->customer_id . '" >';

        }
        $html .= '<div class="mb-2"><label>Route</label><select class="form-control" name="route_id">';
        if ($pickupCustomer){
            $html .= '<option>'.$pickupCustomer->cu_region.'</option>';

        } else {
            $html .= '<option value="">Select One</option>';

        }
        foreach ($routes as $route){
            $html .= '<option>'.$route.'</option>';
        }
        $html .= '</select></div>';

        $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
        $html .= '<button type="button" class="btn btn-primary" onclick="createAutomaticPickup()">Create</button>';
        $html .= '</form>';

        return $html;
    }


}
