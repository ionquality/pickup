<?php

namespace App\Http\Controllers;


use App\Jobs\AddAutomaticPickups;
use App\Models\AutomaticPickup;
use App\Services\Pickup\PickupReportService;
use App\Services\Pickup\PickupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomaticPickupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the list of automatic pickups
     *
     * Database used: AUTO_PICKUPLIST
     *
     * @group Pickups
     * @return void
     */
    public function index() {
        return app()->make(PickupReportService::class)->buildAutomaticPickupList();
    }

    /**
     * Show the atomatic pickup create form
     *
     * Database used: AUTO_PICKUPLIST
     *
     * @group Pickups
     * @return void
     */
    public function create(Request $request) {
        $cu_name = $request->input('cu_name');
        return app()->make(PickupService::class)->buildAutomaticPickupCreateForm($cu_name);
    }


    /**
     * save the pickup in the database
     *
     * Database used: AUTO_PICKUPLIST
     *
     * @group Pickups
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cu_name' => 'required',
            'route_id' => 'required',
            'customer_id' => 'required',
        ]);

        $pickup = AutomaticPickup::create($request->all());


        $msg = 'Customer: '.$pickup->cu_name.' Has Been Added To Automatic Pickup List';
        //Helper::insertLog(now(), 'Agreements', $msg, Auth::user()->id, 'agreement-template-list');
        return response()->json(array('msg' => $msg), 200);
    }

    /**
     * add automatic pickups to pickup list
     *
     * Database used: AUTO_PICKUPLIST
     *
     * @group Pickups
     * @return JsonResponse
     */
    public function deploy(Request $request): JsonResponse
    {
        //$pickup = app()->make(PickupReportService::class)->addAutomaticPickupsToPickupList();
        AddAutomaticPickups::dispatch();
        $msg = 'Automatic Pickups Have Been Added';
        return response()->json(array('msg' => $msg), 200);
    }
    /**
     * delete the automatic pickup
     *
     * Database used: AUTO_PICKUPLIST
     *
     * @group Agreements
     * @return JsonResponse
     */
    public function destroy(AutomaticPickup $pickup): JsonResponse
    {
        $msg = 'Automatic Pickup: '.$pickup->cu_name.' Has Been Removed From the Automatic Pickup List';

        $pickup->delete();

        return response()->json(array('msg' => $msg), 200);
    }


}
