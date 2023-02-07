<?php

namespace App\Http\Controllers;


use App\Services\Pickup\PickupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the agreement view
     *
     * Database used: agreements
     *
     * @group Agreements
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "Home"], ['name' => "Pickup List"]
        ];
        $pageConfigs = [
            'pageHeader' => true
        ];

        return view('/content/pages/pickup/pickupList', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);

    }
    /**
     * Show the agreement
     *
     * Database used: agreements
     *
     * @group Agreements
     * @return void
     */
    public function show(Agreement $agreement) {
        return app()->make(AgreementService::class)->buildAgreementView($agreement);
    }

    /**
     * Show the list of agreements for a consultant
     *
     * Database used: agreements
     *
     * @group Agreements
     * @return void
     */
    public function index() {
        return app()->make(PickupService::class)->buildPickupList();
    }
    /**
     * Show the pickup create form
     *
     * Database used: pickuplist
     *
     * @group Pickups
     * @return void
     */
    public function create(Request $request) {
        $cu_name = $request->input('cu_name');
        return app()->make(PickupService::class)->buildPickupCreateForm($cu_name);
    }


    /**
     * save the pickup in the database
     *
     * Database used: pickuplist
     *
     * @group Pickups
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cu_name' => 'required',
            'route_id' => 'required',
            'pickup_date' => 'required',
        ]);

        $agreementTemplate = AgreementTemplate::find($request->input('agreement_template_id'));
        $agreementArray = $agreementTemplate->toArray();
        $requestArray = array_merge($request->all(), $agreementArray);

        if ($agreementTemplate){
            $agreement = Agreement::create($requestArray);
            $uniqueId = Helper::quickRandomString(16);
            $agreementSearch = Agreement::where('unique_id', $uniqueId)->first();
            while ($agreementSearch){
                $uniqueId = Helper::quickRandomString(16);
                $agreementSearch = Agreement::where('unique_id', $uniqueId)->first();
            }
            $agreement->update(['unique_id' => $uniqueId]);
        }

        $msg = 'Agreement: '.$agreement->name.' Has Been Added To User';
        Helper::insertLog(now(), 'Agreements', $msg, Auth::user()->id, 'agreement-template-list');
        return response()->json(array('msg' => $msg), 200);
    }
    /**
     * save the agreement in the database
     *
     * Database used: agreements
     *
     * @group Agreements
     * @return JsonResponse
     */
    public function complete(Request $request,Agreement $agreement)
    {
        $agreement->update(['complete' => 1, 'complete_date' => now(), 'notes' => $request->input('notes')]);

        $msg = 'Agreement: '.$agreement->name.' Has Been Completed';
        Helper::insertLog(now(), 'Agreements', $msg, 4, 'agreement-template-list');
        return response()->json(array('msg' => $msg), 200);
    }
    /**
     * delete the agreement in the database
     *
     * Database used: agreements
     *
     * @group Agreements
     * @return JsonResponse
     */
    public function destroy(Agreement $agreement): JsonResponse
    {
        $msg = 'Agreement : '.$agreement->name.' Has Been Deleted';

        $agreement->delete();

        Helper::insertLog(now(), 'Agreements', $msg, Auth::user()->id, 'agreement-template-list');
        return response()->json(array('msg' => $msg), 200);
    }
}
