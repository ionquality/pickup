<?php

namespace App\Services\Pickup;


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
        $html = '<h3>Testing Pickup</h3>';

        /*$agreements = AgreementTemplate::orderBy('name')->get();
        $html = '<button class="btn btn-primary" type="button" onclick="createAgreementTemplateForm()">Create Agreement Template</button>';
        $html .= '<div class="table-responsive"><table id="datatable" class="table">';
        $html .= '<thead><tr class="table-primary">';
        $html .= '<th>Name</th><th>Type</th><th>Country</th><th>Approval</th><th>View</th><th>Delete</th></tr></thead>';
        foreach ($agreements as $agreement){
            $approval = $agreement->approval ? 'Yes' : 'No';
            $html .= '<tr>';
            $html .= '<td>'.$agreement->name.'</td><td>'.$agreement->type.'</td>';
            $html .= '<td>'.$agreement->country.'</td>';
            $html .= '<td>'.$approval.'</td>';
            $html .= '<td><a class="btn btn-sm btn-primary" href="agreement-template-view/'.$agreement->id.'"><i class="fa fa-eye"></i></a></td>';
            $html .= '<td><button class="btn btn-sm btn-danger" onclick="deleteAgreementTemplate('.$agreement->id.')"><i class="fa fa-trash"></i></button></td>';
            $html .= '</tr>';
        }
        $html .= '</table></div>';
        */
        return $html;
    }

    /**
     * Build Agreement Template Create Form
     *
     * @group Agreements
     * @return string
     */
    public function buildAgreementTemplateCreateForm(){
        $countries = ["USA", "Canada", "UK", "Mexico", "Italy"];
        $types = ["Consultant", "Auditor", "Employee"];
        $html = '<h3 class="text-primary">Create Agreement Template</h3>';
        $html .= '<form id="createAgreementTemplate">';
        $html .= '<div class="mb-1"><label>Name</label><input class="form-control" name="name"></div>';
        $html .= '<div class="mb-1"><label>Country</label><select class="form-control" name="country"><option value="">Select One</option>';
        foreach ($countries as $country){
            $html .= '<option>'.$country.'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="mb-1"><label>Type</label><select class="form-control" name="type"><option value="">Select One</option>';
        foreach ($types as $type){
            $html .= '<option>'.$type.'</option>';
        }
        $html .= '</select></div>';
        $html .= '<div class="mb-1"><label>Initial Percentage</label><input class="form-control" name="initial_commission_percent"></div>';
        $html .= '<div class="mb-1"><label>Residual Percentage</label><input class="form-control" name="residual_commission_percent"></div>';
        $html .= '<div class="mb-1"><label>Agreement Text</label><textarea class="form-control" name="description"></textarea></div>';

        $html .= '<input type="hidden" name="_token" id="csrf-token" value="' . csrf_token() . '" >';
        $html .= '<input type="hidden" name="created_by" value="'.Auth::user()->id.'">';
        $html .= '<button type="button" class="btn btn-primary" onclick="tinyMCE.triggerSave();createAgreementTemplate()">Create</button>';
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
