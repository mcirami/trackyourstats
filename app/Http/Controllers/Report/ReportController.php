<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use LeadMax\TrackYourStats\Table\Date;

class ReportController extends Controller
{

    public static function getTimezone()
    {
        return (isset($_COOKIE["timezone"])) ? $_COOKIE["timezone"] : "America/Los_Angeles";
    }

    /**
     *
     * @param bool $timezoneConvertDates
     * @return array ['startDate' => 'date', 'endDate' => 'date'
     */
    public static function getDates($timezoneConvertDates = true): array
    {
        $startDate = request()->query('d_from', Carbon::today(self::getTimezone())->format('Y-m-d'));
        $endDate = request()->query('d_to', Carbon::today(self::getTimezone())->format('Y-m-d'));

        $data = [
            'originalStart' => $startDate,
            'originalEnd' => $endDate
        ];


        if ($timezoneConvertDates) {
            Date::addHis($startDate, $endDate);
            $startDate = Date::convertDateTimezone($startDate);
            $endDate = Date::convertDateTimezone($endDate);
        }

        $data = array_merge([
            'startDate' => $startDate,
            'start' => $startDate,
            'end' => $endDate,
            'endDate' => $endDate
        ], $data);

        if (request()->query('debug') == true) {
            dump($data);
        }


        return $data;
    }


}
