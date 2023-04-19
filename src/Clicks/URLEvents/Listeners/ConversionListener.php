<?php

namespace LeadMax\TrackYourStats\Clicks\URLEvents\Listeners;

use LeadMax\TrackYourStats\Clicks\UID;
use LeadMax\TrackYourStats\Clicks\URLEvents\ConversionRegistrationEvent;

class ConversionListener extends Listener
{

    public $GETRequirements = ["clickid"];


    public function dispatch()
    {
        $customPayout = (isset($_GET["price"]) ? $_GET["price"] : false);
	    $percent = (isset($_GET["percentage"]) ? $_GET["percentage"] : false);
	    if ($customPayout && $percent) {
		    $customPayout = round($customPayout * $percent, 3);
		    //$customPayout = bcdiv($customPayout, 1, 2);
	    }
        $clickId      = UID::decode($_GET["clickid"]);
        $register     = new ConversionRegistrationEvent($clickId, $customPayout);

        return $register->fire();
    }

    public function shouldBeDispatched()
    {
        if ($this->checkGETRequirements()) {
            if (isset($_GET["function"]) == false || $_GET["function"] == "") {
                return true;
            }
        }

        return false;
    }

}