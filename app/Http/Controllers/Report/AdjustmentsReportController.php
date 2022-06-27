<?php

namespace App\Http\Controllers\Report;


use App\Http\Controllers\Report\ReportController;
use App\Privilege;
use LeadMax\TrackYourStats\Offer\AdjustmentsLog;
use LeadMax\TrackYourStats\Report\Filters\DollarSign;
use LeadMax\TrackYourStats\Report\Reporter;
use LeadMax\TrackYourStats\Report\Repositories\AdjustmentsLogRepository;
use LeadMax\TrackYourStats\System\Session;
use LeadMax\TrackYourStats\User\Permissions;

class AdjustmentsReportController extends ReportController
{

    /**
     * AdjustmentsReportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:0,1');
        $this->middleware('permissions:' . Permissions::ADJUST_SALES);
    }

    /**
     * Show adjustment reports.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $dates = self::getDates();
        $repo = new AdjustmentsLogRepository(\DB::getPdo());
        $repo->setAction(AdjustmentsLog::ACTION_CREATE_SALE);

        if (Session::userType() == Privilege::ROLE_ADMIN) {
            $repo->showOnlyWithThisSaleLogUserId(Session::userID());
        }

        $reporter = new Reporter($repo);
        $reporter->addFilter(new DollarSign(['paid']));

        return view('report.adjustments', compact('reporter', 'dates'));
    }

}