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
     * Build List of agreement template list
     *
     * @group Consultants
     * @return string
     */
    public function buildPickupList(){

        $pickups = Pickup::join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
            ->select('CUSTOMER.cu_city','pickuplist.*')
            ->where('CUSTOMER.cu_active','Y')
            ->where('pickuplist.complete','N')
            ->where('pickuplist.visible','Y')->get();
        $html = '<button class="btn btn-primary mb-2" type="button" onclick="createPickupForm()">Add Pickup</button>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>City</th><th>Comments</th><th>Pickup Date</th></tr></thead>';
        foreach ($pickups as $pickup){
            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.$pickup->cu_city.'</td><td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateString($pickup->pickup_date).'</td>';
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

        $pickups = Pickup::join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
            ->select('CUSTOMER.cu_city','pickuplist.*')
            ->where('CUSTOMER.cu_active','Y')
            ->where('route_id',$route_id)
            ->where('pickuplist.complete','N')
            ->where('pickuplist.visible','Y')->get();
        $html = '<h3>Driver '.$route_id.'</h3>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Route</th><th>Customer</th><th>Location</th><th>Comments</th><th>Pickup Date</th><th>Complete</th></tr></thead>';
        foreach ($pickups as $pickup){
            $pickupCustomer = Customer::select('cu_name','cu_region','cu_address_1','cu_address_2','cu_city','cu_state','cu_zip')
                ->where('cu_name',$pickup->cu_name)->first();

            $html .= '<tr>';
            $html .= '<td>'.$pickup->route_id.'</td><td>'.$pickup->cu_name.'</td>';
            $html .= '<td>'.Helpers::getCustomerAddress($pickupCustomer).'</td><td>'.$pickup->comments.'</td>';
            $html .= '<td>'.Helpers::getDateString($pickup->pickup_date).'</td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';

        return $html;
    }

    /**
     * Build Agreement Template Create Form
     *
     * @group Agreements
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
     * Build Agreement Template Create Form
     *
     * @group Agreements
     * @return string
     */
    public function buildAgreementTemplateEditForm(AgreementTemplate $agreement){
        $countries = ["USA", "Canada", "UK", "Mexico", "Italy"];
        $types = ["Consultant", "Auditor", "Employee"];
        $html = '<h3 class="text-primary">Edit Agreement Template</h3>';
        $html .= '<form id="editAgreementTemplate">';
        $html .= '<div class="mb-1"><label>Name</label><input class="form-control" name="name" value="'.$agreement->name.'"></div>';
        $html .= '<div class="mb-1"><label>Country</label><select class="form-control" name="country"><option>'.$agreement->country.'</option>';
        foreach ($countries as $country){
            $html .= '<option>'.$country.'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="mb-1"><label>Type</label><select class="form-control" name="type"><option>'.$agreement->type.'</option>';
        foreach ($types as $type){
            $html .= '<option>'.$type.'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="mb-1"><label>Initial Percentage</label><input class="form-control" name="initial_commission_percent" value="'.$agreement->initial_commission_percent.'"></div>';
        $html .= '<div class="mb-1"><label>Residual Percentage</label><input class="form-control" name="residual_commission_percent" value="'.$agreement->residual_commission_percent.'"></div>';
        $html .= '<div class="mb-1"><label>Agreement Text</label><textarea class="form-control" name="description">'.$agreement->description.'</textarea></div>';

        $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
        $html .= '<input type="hidden" name="created_by" value="'.Auth::user()->id.'">';
        $html .= '<button type="button" class="btn btn-primary" onclick="tinyMCE.triggerSave();editAgreementTemplate('.$agreement->id.')">Save</button>';
        $html .= '</form>';

        return $html;
    }

    /**
     * Build Agreement Template View
     *
     * @group Agreements
     * @return string
     */
    public function buildAgreementTemplateView(AgreementTemplate $agreementTemplate){
        $html = '<div class="row">';
        $html .= '<div class="col-md-3">';
        $html .= '<div class="d-grid gap-1">';
        $html .= '<button class="btn btn-primary" onclick="editAgreementTemplateForm('.$agreementTemplate->id.')">Edit</button>';
        $html .= '<button class="btn btn-primary" onclick="agreementTemplateApproveForm('.$agreementTemplate->id.')">Approve</button>';
        $html .= '<button class="btn btn-primary" onclick="agreementTemplatePreview('.$agreementTemplate->id.')">Preview</button>';
        $html .= '<button class="btn btn-primary" >Assign</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-9">';
        $html .= '<div class="card"><div class="card-content"><div class="card-body">';
        $html .= '<div id="agreement-detail"></div>';
        $html .= '</div></div></div></div></div>';

        return $html;
    }

    /**
     * Build Agreement Template Approve Form
     *
     * @group Agreements
     * @return string
     */
    public function buildAgreementTemplateApproveForm(AgreementTemplate $agreementTemplate){
        if ($agreementTemplate->approval){
            $headerMessage = '<h3 class="text-primary">Unapprove The Agreement Template</h3>';
            $message = '<p>Click the button below to unapprove the Agreement Template</p>';
            $button = 'Unapprove';
            $approvalMessage = '<div class="alert alert-success" role="alert">';
            $approvalMessage .= '<p>Approved on: '.Helper::getDateString($agreementTemplate->approval_date).'</p>';
            $approvalMessage .= '<p>Approved By: '.Helper::getUserName($agreementTemplate->approved_by).'</p>';
            $approvalMessage .= '</div>';
        } else {
            $headerMessage = '<h3 class="text-primary">Approve The Agreement Template</h3>';
            $message = '<p>Click the button below to approve the Agreement Template</p>';
            $button = 'Approve';
            $approvalMessage = '<div class="alert alert-danger" role="alert">';
            $approvalMessage .= '<p>This template has not been approved</p>';
            $approvalMessage .= '</div>';
        }
        $html = $approvalMessage;
        $html .= $headerMessage;
        $html .= $message;
        $html .= '<form id="approveAgreementTemplate">';
        $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
        $html .= '<button type="button" class="btn btn-primary" onclick="approveAgreementTemplate('.$agreementTemplate->id.')">'.$button.'</button>';
        $html .= '</form>';

        return $html;
    }

    /**
     * Build Agreement Template Preview
     *
     * @group Agreements
     * @return string
     */
    public function buildAgreementTemplatePreview(AgreementTemplate $agreementTemplate){
        return $agreementTemplate->description;
    }

}
