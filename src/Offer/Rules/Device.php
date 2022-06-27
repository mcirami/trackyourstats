<?php
/**
 * Created by PhpStorm.
 * User: dean
 * Date: 8/16/2017
 * Time: 10:30 AM
 */

namespace LeadMax\TrackYourStats\Offer\Rules;


use Detection\MobileDetect;


class Device implements Rule
{

    // rules passed from Rules class that was associated with passed offer id
    private $rules = array();


    private $filteredRules = array();

    // gets instatiacted to mobile detect obj
    private $detect;

    public $redirectOffer = 0;

    function __construct($rules)
    {
        // sets rules from Rules class
        $this->rules = $rules;

        // finds device rules and sets allowedDeviceType
        $this->processRules();

        // instantiate mobile detect
        $this->detect = new MobileDetect();

    }

    public function getRedirectOffer()
    {
        return $this->redirectOffer;
    }


    // checks device rules
    public function checkRules()
    {

        if (empty($this->filteredRules)) {
            return true;
        }

        foreach ($this->filteredRules as $rule) {
            $this->redirectOffer = $rule["redirect_offer"];

            switch ($rule["device_type"]) {
                case "desktop":
                    if ($rule["deny"] == 1) {
                        return $this->detect->isMobile();
                    } else {
                        return !$this->detect->isMobile();
                    }
                    break;

                case "mobile":
                    if ($rule["deny"] == 1) {
                        return !$this->detect->isMobile();
                    } else {
                        return $this->detect->isMobile();
                    }
                    break;

            }


        }

        return false;
    }

    private function processRules()
    {

        // loops through rules and finds if its a device type
        foreach ($this->rules as $key => $val) {

            if ($val["type"] == "device" && $val["is_active"] == 1) // if it is, added the allowed device type to our var
            {
                if (!isset($this->filteredRules[$val["idrule"]])) {
                    $this->filteredRules[$val["idrule"]] = array();
                    $this->filteredRules[$val["idrule"]]["device_type"] = $val["device_type"];
                    $this->filteredRules[$val["idrule"]]["redirect_offer"] = $val["redirect_offer"];
                    $this->filteredRules[$val["idrule"]]["deny"] = $val["deny"];

                }

            }

        }


    }

}