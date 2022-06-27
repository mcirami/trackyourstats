<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OfferCap
 *
 * @property int $offer_idoffer
 * @property int $type
 * @property int $time_interval
 * @property int $interval_cap
 * @property int $redirect_offer
 * @property int $status 1 is enabled, 0 is disabled
 * @property int $is_capped
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereIntervalCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereIsCapped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereOfferIdoffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereRedirectOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereTimeInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\OfferCap whereType($value)
 * @mixin \Eloquent
 */
class OfferCap extends Model
{

    // ehehehehe
    protected $primaryKey = 'offer_idoffer';

    public $timestamps = false;

    const TYPE_CLICKS = 0;
    const TYPE_CONVERSIONS = 1;

    const INTERVAL_DAILY = 0;
    const INTERVAL_WEEKLY = 1;
    const INTERVAL_MONTHLY = 2;
    const INTERVAL_TOTAL = 3;

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

}
