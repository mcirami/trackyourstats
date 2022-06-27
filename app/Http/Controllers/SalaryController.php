<?php

namespace App\Http\Controllers;

use App\Privilege;
use App\Salary;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LeadMax\TrackYourStats\System\Session;
use LeadMax\TrackYourStats\User\Permissions;

class SalaryController extends Controller
{

    /**
     * SalaryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissions:' . Permissions::EDIT_SALARIES);
    }


    /**
     * Show the form to create a salary for a user.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        $user = User::withRole(Privilege::ROLE_AFFILIATE)->myUsers()->findOrFail($id);


        return view('salary.create', compact('user'));
    }

    /**
     * Create a salary for a user.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($id, Request $request)
    {
        $user = User::withRole(Privilege::ROLE_AFFILIATE)->myUsers()->findOrFail($id);

        $this->validate($request, [
            'salary' => 'required|numeric',
            'status' => 'required|numeric',
        ]);

        $salary = new Salary;
        $salary->salary = $request->input('salary');
        $salary->status = $request->input('status');
        $salary->timestamp = Carbon::now()->timestamp;
        $salary->last_update = Carbon::now()->timestamp;

        $user->salary()->save($salary);


        return redirect()->route('salary.update', $id);
    }

    /**
     * Show the form to edit a users salary.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::withRole(Privilege::ROLE_AFFILIATE)->myUsers()->findOrFail($id);

        $salary = $user->salary;


        return view('salary.update', compact('salary', 'user'));
    }

    /**
     * Update a users salary.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $user = User::withRole(Privilege::ROLE_AFFILIATE)->myUsers()->findOrFail($id);

        $salary = $user->salary;

        $this->validate($request, [
            'salary' => 'required|numeric',
            'status' => 'required|numeric',
        ]);

        $salary->salary = $request->input('salary');
        $salary->status = $request->input('status');
        $salary->last_update = Carbon::now()->timestamp;

        $salary->save();


        return back()->with(['messages' => ['Success']]);
    }
}
