<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlackListReportController extends ReportController
{
    /**
     * BlackListReportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:0');
    }

    /**
     * Show blacklisted clicks.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $dates = self::getDates();
        $report = new \LeadMax\TrackYourStats\Report\BlackList(new \LeadMax\TrackYourStats\Report\Repositories\BlackListRepository());

        $reps = $report->getReport($dates['startDate'], $dates['endDate']);

        return view('report.blacklist', compact('reps'));
    }

}
