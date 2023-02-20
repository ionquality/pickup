<?php

namespace App\Http\Controllers;


use App\Jobs\AddAutomaticPickups;
use App\Models\AutomaticPickup;
use App\Models\User;
use App\Services\Pickup\PickupReportService;
use App\Services\Pickup\PickupService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user view
     *
     * Database used: users
     *
     * @group User
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view(User $user)
    {

        $pageConfigs = [
            'pageHeader' => true
        ];

        return view('/content/pages/pickup/user', [
            'pageConfigs' => $pageConfigs,
            'title' => $user->name,
            'user_id' => $user->id
        ]);

    }

    /**
     * Show the avatar create form
     *
     * Database used: users
     *
     * @group User
     * @return void
     */
    public function avatar(User $user) {
        return app()->make(UserService::class)->buildAvatarForm($user);
    }


    /**
     * udpate user avatar
     *
     * Database used: users
     *
     * @group User
     * @return JsonResponse
     */
    public function update(User $user, Request $request): JsonResponse
    {
        $user->update($request->all());

        $msg = 'User: '.$user->name.' Profile Updated';
        return response()->json(array('msg' => $msg), 200);
    }

}
