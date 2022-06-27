<?php

namespace LeadMax\TrackYourStats\Clicks\URLEvents;


use App\BonusOffer;
use App\User;
use LeadMax\TrackYourStats\Clicks\Click;
use LeadMax\TrackYourStats\Clicks\Conversion;
use LeadMax\TrackYourStats\Clicks\Cookie;
use LeadMax\TrackYourStats\Clicks\UID;
use LeadMax\TrackYourStats\Clicks\URLProcessor;
use LeadMax\TrackYourStats\Clicks\URLTagReplacers\Base64;
use LeadMax\TrackYourStats\Clicks\URLTagReplacers\SubVariables;
use LeadMax\TrackYourStats\Clicks\URLTagReplacers\TYSVariables;
use LeadMax\TrackYourStats\Offer\Caps;
use LeadMax\TrackYourStats\Offer\Offer;
use LeadMax\TrackYourStats\Offer\RepHasOffer;
use LeadMax\TrackYourStats\Offer\Rules;
use LeadMax\TrackYourStats\System\IPBlackList;

class ClickRegistrationEvent extends URLEvent
{

    public $subVarArray = [];


    public $offerId;

    public $userId;

    public function __construct($user_id, $offer_id, $sub_variables_array)
    {
        $this->userId = $user_id;
        $this->offerId = $offer_id;
        $this->subVarArray = $sub_variables_array;
    }

    public static function getEventString(): string
    {
        return "click";
    }

    public function fire()
    {
        if ($this->registerClick()) {
            $this->sendUserToOffer();
        } else {
            return false;
        }
    }

    private function getClickType()
    {
        $blacklist = new IPBlackList($_SERVER["REMOTE_ADDR"]);

        if ($blacklist->isBlackListed()) {
            return Click::TYPE_BLACKLISTED;
        }

        $cookie = new Cookie($this->userId, $this->offerId);
        $cookie->setPreventTransferCookie();
        if ($cookie->isUnique()) {
            return Click::TYPE_UNIQUE;
        } else {
            return Click::TYPE_RAW;
        }

    }

    private function registerClick()
    {
        if ($this->validateOffer() && $this->validateUser()) {

            if (!$this->checkBonusOfferRequirementMet()) {
                return false;
            }

            $click = new Click();

            $click->first_timestamp = date("Y-m-d H:i:s");
            $click->ip_address = $_SERVER["REMOTE_ADDR"];
            $click->browser_agent = $_SERVER["HTTP_USER_AGENT"];

            $click->rep_idrep = $this->userId;
            $click->offer_idoffer = $this->offerId;
            $click->click_type = $this->getClickType();

            if ($click->save()) {
                $cookie = new Cookie($this->userId, $this->offerId);
                $cookie->registerClick();
                $cookie->save();
            }


            $this->clickId = $click->id;


            if ($this->offerData->offer_type == Offer::TYPE_CPC && $click->click_type == Click::TYPE_UNIQUE) {

                $customPrice = isset($_GET["price"]) ? $_GET["price"] : false;

                $conversion = new Conversion($click->id);

                if ($customPrice) {
                    $conversion->paid = $customPrice;
                }

                $conversion->registerSale();
            }


            return true;
        } else {
            return false;
        }
    }


    private function validateUser()
    {
        $this->getUserDataFromDatabase($this->userId);

        if ($this->userData == false) {
            return false;
        }

        if ($this->userData->status != 1) {
            return false;
        }


        if (User::find($this->userId)->isBanned()) {
            return false;
        }

        if (RepHasOffer::doesAffiliateOwnOffer($this->userId, $this->offerId) == false) {
            return false;
        }

        return true;
    }

    private function validateOffer()
    {
        $this->checkIfOfferCappedAndSendToRedirectIfCapped();

        $this->getOfferDataFromDatabase($this->offerId);

        if ($this->offerData->status == 1) {
            if ($this->checkOfferRules()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    private function checkOfferRules()
    {
        $rules = new Rules($this->offerId);
        if ($rules->checkAllRules() == true) {
            return true;
        } else {
            return false;
        }
    }

    private function checkIfOfferCappedAndSendToRedirectIfCapped()
    {
        $caps = new Caps($this->offerId);

        if ($caps->isOfferCapped()) {
            $caps->sendToRedirectOffer();
        }

    }

    private function sendUserToOffer()
    {
        $user_id = $this->userId;
        $this->getUserDataFromDatabase($user_id);

        $offer_id = $this->offerId;
        $this->getOfferDataFromDatabase($offer_id);

        $encodedClickId = UID::encode($this->clickId);


        $subVarReplacer = new SubVariables($this->subVarArray);
        $tysReplacer = new TYSVariables($user_id, $this->userData->user_name, $encodedClickId, $offer_id, $this->userData->referrer_repid,$this->adminId);

        $urlProcessor = new URLProcessor($this->offerData->url);
        $urlProcessor->addTagReplacer($subVarReplacer);
        $urlProcessor->addTagReplacer($tysReplacer);
        $urlProcessor->addTagReplacer(new Base64());

        $urlProcessor->processURL();

        $urlProcessor->sendUserToUrl();
    }

    private function checkBonusOfferRequirementMet()
    {
        $bonusOffer = BonusOffer::where('offer_id', '=', $this->offerId)->first();

        if (is_null($bonusOffer)) {
            return true;
        }


        return $bonusOffer->canAffiliateUseOffer($this->userId);
    }

}