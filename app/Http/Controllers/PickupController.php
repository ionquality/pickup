<?php

namespace App\Http\Controllers;


use App\Models\Pickup;
use App\Services\Pickup\PickupReportService;
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

        $pageConfigs = [
            'pageHeader' => true
        ];

        if (Auth::user()->op_id == 50114){
            return view('/content/pages/pickup/driver', [
                'pageConfigs' => $pageConfigs,
                'route_id' => '5'
            ]);
        } elseif (Auth::user()->op_id == 30414) {
            return view('/content/pages/pickup/driver', [
                'pageConfigs' => $pageConfigs,
                'route_id' => '6'
            ]);
        } elseif (Auth::user()->op_id == 428142) {
            return view('/content/pages/pickup/driver', [
                'pageConfigs' => $pageConfigs,
                'route_id' => '8'
            ]);
        } elseif (Auth::user()->op_id == 30514) {
            return view('/content/pages/pickup/driver', [
                'pageConfigs' => $pageConfigs,
                'route_id' => '3'
            ]);
        }
        else {
            return view('/content/pages/pickup/pickupList', [
                'pageConfigs' => $pageConfigs,
            ]);
        }
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
     * Show the report view
     *
     * Database used: pickuplist
     *
     * @group Agreements
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function reportView()
    {
        $pageConfigs = [
            'pageHeader' => true
        ];

        return view('/content/pages/pickup/report', [
            'pageConfigs' => $pageConfigs
        ]);

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
     * Show the pickup edit form
     *
     * Database used: pickuplist
     *
     * @group Pickups
     * @return void
     */
    public function edit(Pickup $pickup) {
        return app()->make(PickupService::class)->buildPickupEditForm($pickup);
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
     * uodate the pickup in the database
     *
     * Database used: pickuplist
     *
     * @group Pickups
     * @return JsonResponse
     */
    public function update(Pickup $pickup,Request $request): JsonResponse
    {
        $request->validate([
            'pickup_date' => 'required',
        ]);

        $pickup->update($request->all());

        $msg = 'Customer: '.$pickup->cu_name.' Has Been Updated In The Pickup List';
        //Helper::insertLog(now(), 'Agreements', $msg, Auth::user()->id, 'agreement-template-list');
        return response()->json(array('msg' => $msg), 200);
    }
    /**
     * Complete the pickup
     *
     * Database used: pickuplist
     *
     * @group Pickup
     * @return JsonResponse
     */
    public function complete(Pickup $pickup)
    {
        $pickup->update(['complete' => 'Y', 'complete_date' => now(), 'notification' => 'N']);
        Pickup::where('route_id',$pickup->route_id)->update(['notification' => 'N']);

        $msg = 'Pickup: '.$pickup->cu_name.' Has Been Completed';
        return response()->json(array('msg' => $msg), 200);
    }

    /**
     * Dismiss Notifications
     *
     * Database used: pickuplist
     *
     * @group Pickup
     * @return JsonResponse
     */
    public function notification(Request $request)
    {
        Pickup::where('route_id',$request->input('route_id'))->update(['notification' => 'N']);

        $msg = 'Notifications Have Been Cleared';
        return response()->json(array('msg' => $msg), 200);
    }
    /**
     * delete the pickup
     *
     * Database used: pickuplist
     *
     * @group Agreements
     * @return JsonResponse
     */
    public function destroy(Pickup $pickup): JsonResponse
    {
        $pickup->update(['visible' => 'N','remove_op_id' => Auth::user()->id, 'remove_date' => now()]);

        $msg = 'Pickup: '.$pickup->cu_name.' Has Been Deleted';

        return response()->json(array('msg' => $msg), 200);
    }

    /**
     * Build Report Views
     *
     * Database used: pickuplist
     *
     * @group Pickups
     * @return void
     */
    public function report(Request $request) {
        $type = $request->input('type');
        $html = '';
        if ($type == 'Deleted'){
            $html = app()->make(PickupReportService::class)->buildDeletedPickupList($request);
        } elseif ($type == 'All'){
            $html .= app()->make(PickupReportService::class)->buildAllPickupList($request);
        } elseif ($type == 'Automatic'){
            $html .= app()->make(PickupReportService::class)->buildAutomaticPickupList($request);
        }
        return $html;
    }
}
