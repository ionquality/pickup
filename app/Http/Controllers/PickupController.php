<?php

namespace App\Http\Controllers;


use App\Models\Pickup;
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
     * Show the pickup view
     *
     * Database used: pickuplist
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
     * Show the pickup driver view
     *
     * Database used: pickuplist
     *
     * @group Agreements
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function driverView()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "Home"], ['name' => "Drivers"]
        ];
        $pageConfigs = [
            'pageHeader' => true
        ];

        return view('/content/pages/pickup/driver', [
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
     * Show the list of pickups for a driver
     *
     * Database used: pickuplist
     *
     * @group Pickup
     * @return void
     */
    public function driverIndex(Request $request) {
        return app()->make(PickupService::class)->buildPickupDriverList($request->input('route_id'));
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
        $count = Pickup::count();
        $count ++;
        $pickup_seq = Pickup::join('CUSTOMER','pickuplist.cu_name','=','CUSTOMER.cu_name')
            ->where('CUSTOMER.cu_active','Y')
            ->where('pickuplist.complete','N')
            ->where('pickuplist.visible','Y')
            ->where('pickuplist.route_id', $request->input('route_id'))
            ->count();
        $pickup_seq++;
        $request["seqno"] = $count;
        $request["pickup_seqno"] = $pickup_seq;
        $pickup = Pickup::create($request->all());


        $msg = 'Customer: '.$pickup->cu_name.' Has Been Added To Pickup List';
        //Helper::insertLog(now(), 'Agreements', $msg, Auth::user()->id, 'agreement-template-list');
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
