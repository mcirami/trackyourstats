<?php

namespace App;

use App\Traits\InsertOnDuplicateKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use LeadMax\TrackYourStats\Offer\Caps;
use LeadMax\TrackYourStats\User\Permissions;

/**
 * App\Offer
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $affiliates
 * @mixin \Eloquent
 * @property int $idoffer
 * @property int $created_by
 * @property string|null $offer_name
 * @property string|null $description
 * @property string $url
 * @property int $offer_type
 * @property int|null $is_public
 * @property float|null $payout
 * @property int|null $status
 * @property string|null $offer_timestamp
 * @property int $campaign_id
 * @property int|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereIdoffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereOfferName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereOfferTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereOfferType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer wherePayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Offer whereUrl($value)
 * @property-read \App\Campaign $campaign
 * @property-read \App\OfferCap $cap
 * @property-read \App\OfferBonus $bonus
 */
class Offer extends Model
{

    const TYPE_CPA = 0;
    const TYPE_CPC = 1;
    const TYPE_BLACKLISTED = 2;
    const TYPE_PENDING_CONVERSION = 3;

    CONST VISIBILITY_PRIVATE = 0;
    const VISIBILITY_PUBLIC = 1;
    CONST VISIBILITY_REQUESTABLE = 2;

    protected $fillable = [
        'offer_name',
        'description',
        'url',
        'offer_type',
        'payout',
        'status',
        'offer_timestamp',
        'is_public',
        'campaign_id',
        'parent',
    ];

    public $timestamps = false;
    protected $table = 'offer';
    protected $primaryKey = 'idoffer';

    /**
     * Assign users to this offer.
     * @param Collection $users
     * @return int
     */
    public function assignUsers($users)
    {
        if ($users->first()->getRole() != User::ROLE_AFFILIATE) {
            $users = User::withRole(User::ROLE_AFFILIATE)->whereIn('referrer_repid', $users->pluck('idrep'))->get();
        }
        $insert = [];
        foreach ($users as $user) {
            $insert[] = ['rep_idrep' => $user->idrep, 'offer_idoffer' => $this->idoffer, 'payout' => $this->payout];
        }

        return UserOffer::insertIgnore($insert);
    }

    /**
     * Remove users from the offer.
     * @param Collection $users
     * @return mixed
     */
    public function removeUsers($users)
    {
        if ($users->first()->getRole() != User::ROLE_AFFILIATE) {
            $users = User::withRole(User::ROLE_AFFILIATE)->whereIn('referrer_repid', $users->pluck('idrep'))->get();
        }
        return UserOffer::query()
            ->whereIn('rep_idrep', $users->pluck('idrep')->toArray())
            ->where('offer_idoffer', $this->idoffer)
            ->delete();
    }

    /**
     * Offer has one OfferCap.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cap()
    {
        return $this->hasOne(OfferCap::class);
    }

    /**
     * Offer has one BonusOffer.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bonus()
    {
        return $this->hasOne(BonusOffer::class, 'offer_id');
    }

    /**
     * Offers belong to one campaign.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Offers have many affiliates.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function affiliates()
    {
        return $this->belongsToMany(User::class, 'rep_has_offer', 'offer_idoffer', 'rep_idrep');
    }

}
