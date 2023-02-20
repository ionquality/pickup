<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Models\Customer;
use App\Models\Pickup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @group Pickups
 *
 * This service handles the display of Pickups
 */
class UserService
{
    /**
     * Build User Avatar Form
     *
     * @group Pickups
     * @return string
     */
    public function buildAvatarForm(User $user){
        $files = Storage::disk('public')->files('avatars');
        $html = '<h3 class="text-primary">Update Avatar</h3>';
        $html .= '<form id="updateUser">';
        $index = 1;
        foreach ($files as $f) {
            $check = $user->profile_photo_path == $f ? 'checked' :'';
            $html .= ' <div class="form-check form-check-inline mt-3">';
            $html .= '<input class="form-check-input" type="radio" name="profile_photo_path" id="image-'.$index.'" value="' . $f . '" '.$check.'/>';
            $html .= '<label class="form-check-label" for="image-'.$index.'"><img src="/storage/' . $f . '" style="height:50px"></label>';
            $html .= '</div>';
            $index++;
        }

        $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
        $html .= '<br><br><button type="button" class="btn btn-primary" onclick="updateUser()">Save</button>';
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
