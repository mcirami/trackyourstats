<?php

namespace App\Http\Controllers;

use App\Privilege;
use App\User;
use Illuminate\Http\Request;
use LeadMax\TrackYourStats\Table\Paginate;
use LeadMax\TrackYourStats\User\Permissions;

class UserController extends Controller
{

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:0,1,2')->only('viewManagersAffiliates');
        $this->middleware('permissions:' . Permissions::CREATE_MANAGERS)->only('viewManagersAffiliates');
    }

    public function viewManagersAffiliates($id)
    {
        $manager = User::myUsers()->withRole(Privilege::ROLE_MANAGER)->findOrFail($id);


        $affiliates = $manager->users()->withRole(Privilege::ROLE_AFFILIATE)->with('referrer');

        $paginate = new Paginate(request('rpp', 10), $affiliates->count());

        $affiliates = $affiliates->paginate(request('rpp', 10));

        return view('user.managers-affiliates', compact('manager', 'affiliates', 'paginate'));
    }

    /**
     * View users.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->validate(request(), [
            'showInactive' => 'numeric|min:0|max:1'
        ]);

        $users = User::myUsers()->withRole(request('role', Privilege::ROLE_AFFILIATE))->with('referrer');

        if (request('showInactive', 0) == 1) {
            $users->where('status', 0);
        } else {
            $users->where('status', 1);
        }

        $users = $users->get();


        return view('user.manage', compact('users'));
    }

}
