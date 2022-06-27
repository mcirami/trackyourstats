<?php

namespace App\Http\Controllers;


use App\BonusOffer;
use App\Campaign;
use App\Http\Requests\StoreOffer;
use App\Http\Requests\UpdateOffer;
use App\Offer;
use App\OfferBonus;
use App\OfferCap;
use App\OfferURL;
use App\Privilege;
use App\User;
use App\UserOffer;
use Carbon\Carbon;
use Faker\Provider\PhoneNumber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use LeadMax\TrackYourStats\Offer\Campaigns;
use LeadMax\TrackYourStats\Offer\URLs;
use LeadMax\TrackYourStats\System\Company;
use LeadMax\TrackYourStats\Table\Paginate;
use LeadMax\TrackYourStats\User\Permissions;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:0')->only('dupe', 'delete');
        $this->middleware('role:3')->only('requestOffer');
        $this->middleware('permissions:' . Permissions::CREATE_OFFERS)->only([
            'create',
            'store',
            'edit',
            'createMassAssign',
            'storeMassAssign',
            'getAssignableUsers'
        ]);
    }

    /**
     * Show the users offers.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array();

        $this->validate(request(), [
            'showInactive' => 'numeric|min:0|max:1'
        ]);

        $urls = \App\Company::instance()->first()->offerUrls()->where('status', 1)->get();
        /* @var $urls Collection */
        if ($urls->isEmpty()) {
            $url = new OfferURL();
            $url->url = request()->getHttpHost();
            $urls->add($url);
        }
        $urls = $urls->pluck('url')->toArray();
        $data['urls'] = $urls;


        $status = request('showInactive', 0) == 1 ? 0 : 1;
        $offers = \LeadMax\TrackYourStats\System\Session::user()->offers()->where('offer.status', '=', $status);

        if (\LeadMax\TrackYourStats\System\Session::userType() == Privilege::ROLE_AFFILIATE) {
            $offers->leftJoin('bonus_offers', 'bonus_offers.offer_id', '=', 'offer.idoffer');
            $data['requestableOffers'] = Offer::where('is_public',
                \LeadMax\TrackYourStats\Offer\Offer::VISIBILITY_REQUESTABLE)
                ->whereRaw('offer.idoffer NOT IN (SELECT offer_idoffer FROM rep_has_offer WHERE rep_has_offer.rep_idrep = ' . \LeadMax\TrackYourStats\System\Session::userID() . ')')->get();
        }

        if (request()->has('search')) {
            $offers->where('offer_name', 'LIKE', '%' . \request('search') . '%');
            $offers->orWhere('idoffer', 'LIKE', '%' . \request('search') . '%');
        }


        $paginate = new Paginate(request('rpp', 25), $offers->count());
        $offers = $offers->paginate(request('rpp', 25));

        $data = array_merge(compact('paginate', 'offers'), $data);


        return view('offer.manage', $data);
    }

    /**
     * Show the form to create an offer.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $offer = Session::get('offer') ?: new Offer();

        return view('offer.create')->with(['offer' => $offer]);
    }

    /**
     * Store an offer.
     * @param StoreOffer $request
     * @throws \Exception
     */
    public function store(StoreOffer $request)
    {
        DB::beginTransaction();
        $offer = new Offer($request->all());
        if (!$request->has('campaign_id')) {
            $offer->campaign_id = Campaigns::getDefaultCampaignId();
        }
        $offer->offer_timestamp = Carbon::now('UTC')->format('Y-m-d H:i:s');
        $offer->created_by = \LeadMax\TrackYourStats\System\Session::user()->idrep;
        $offer->save();

        $users = User::myUsers()->whereIn('idrep', $request->users);
        if ($users->first()->role != \App\Privilege::ROLE_AFFILIATE) {
            $users = User::withRole(\App\Privilege::ROLE_AFFILIATE)->whereIn('referrer_repid', $users->pluck('idrep'));
        }

        foreach ($users as $user) {
            $userOffer = new UserOffer();
            $userOffer->rep_idrep = $user->idrep;
            $userOffer->offer_idoffer = $offer->idoffer;
            $userOffer->payout = $offer->payout;
            $userOffer->save();
        }
        DB::commit();
    }

    /**
     * Show the form to edit an offer.
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $this->validate($request, [
            'user_type' => 'numeric|min:2|max:3'
        ]);
        $offer = Offer::with('cap', 'bonus')->findOrFail($id);
        $redirectableOffers = Offer::where('idoffer', '!=', $offer->idoffer)->get();
        $campaigns = Campaign::all();

        $assignedUsers = $offer->affiliates()
            ->myUsers()
            ->withRole(User::ROLE_AFFILIATE)->get();
        if (request('user_type', User::ROLE_AFFILIATE) == User::ROLE_AFFILIATE) {
            $unAssignedUsers = User::myUsers()
                ->withRole(User::ROLE_AFFILIATE)
                ->whereNotIn('idrep', $assignedUsers->pluck('idrep'))->get();
        } else {
            $unAssignedUsers = [];
            $assignedUsers = User::withRole(User::ROLE_MANAGER)->myUsers()->get();
            // TODO(vulski): This is pretty convoluted and confusing logic. Should get some cleanup, maybe move to a template ?  (Still better than the old code though)
            foreach ($assignedUsers as $key => $manager) {
                $managersUsers = $manager->users()->withRole(User::ROLE_AFFILIATE)->get();
                $assignedCount = $offer->affiliates()->whereIn('rep_idrep', $managersUsers->pluck('idrep'))->count();
                if ($assignedCount != 0) {
                    $manager->user_name .= " - $assignedCount affiliate(s) assigned.";
                }
                if ($assignedCount == $managersUsers->count()) {
                    $manager->user_name .= " (All)";
                } else {
                    $unAssignedUsers[] = $manager;
                    unset($assignedUsers[$key]);
                }
            }
        }

        return view('offer.edit',
            compact('offer', 'campaigns', 'assignedUsers', 'unAssignedUsers', 'redirectableOffers'));
    }

    /**
     * @param UpdateOffer $request
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function update(UpdateOffer $request, $id)
    {
        /** @var Offer $offer */
        $offer = Offer::findOrFail($id);
        $offer->offer_name = $request->input('offer_name');
        $offer->description = $request->input('description');
        $offer->is_public = $request->input('visibility');
        if ($request->has('campaign')) {
            $campaign = Campaign::findOrFail($request->input('campaign'));
            $offer->campaign_id = $campaign->id;
        }
        $offer->status = $request->input('status');
        $offer->offer_type = $request->input('offer_type');
        $offer->description = $request->input('description');
        $offer->url = $request->input('url');
        $offer->payout = $request->input('payout');
        DB::beginTransaction();
        $offer->save();

        if ($request->has('assigned')) {
            $users = User::myUsers()->whereIn('idrep', $request->input('assigned'))->get();
            $offer->assignUsers($users);
        }

        if ($request->has('unassigned')) {
            $users = User::myUsers()->whereIn('idrep', $request->input('unassigned'))->get();
            $offer->removeUsers($users);
        }

        $cap = OfferCap::where('offer_idoffer', $offer->idoffer)->first();
        if ($request->has('enable_cap')) {
            if (is_null($offer->cap)) {
                $cap = new OfferCap();
            }
            $cap->type = $request->input('cap_type');
            $cap->status = OfferCap::STATUS_ENABLED;
            $cap->time_interval = $request->input('cap_interval');
            $cap->interval_cap = $request->input('interval_cap');
            $cap->offer_idoffer = $offer->idoffer;
            $cap->redirect_offer = $request->input('redirect_offer');
            $cap->save();
        } else {
            if (!is_null($cap)) {
                $cap->status = OfferCap::STATUS_DISABLED;
                $cap->save();
            }
        }

        $bonus = BonusOffer::where('offer_id', $offer->idoffer)->first();
        if ($request->has('enable_bonus_offer')) {
            if (is_null($bonus)) {
                $bonus = new BonusOffer();
            }
            $bonus->required_sales = $request->input('required_sales');
            $bonus->offer_id = $offer->idoffer;
            $bonus->active = 1;
            $bonus->save();
        } else {
            if (!is_null($bonus)) {
                $bonus->active = 0;
                $bonus->save();
            }
        }

        DB::commit();


        session()->flash('message', 'Success.');

        return back();
    }

    /**
     *
     * @return string
     */
    public function getAssignableUsers()
    {
        return User::withRole(Input::get('user_type') === Privilege::ROLE_MANAGER ? Privilege::ROLE_MANAGER : Privilege::ROLE_AFFILIATE)
            ->myUsers()->select(['rep.idrep as id', 'rep.user_name as name'])->get()->toJson();
    }

    /**
     * Get assignable users for an offer.
     * @param $offerId
     * @return string
     */
    public function getAssignedUsers($offerId)
    {
        $offer = Offer::where('idoffer', '=', $offerId)->first();

        return $offer->affiliates()->get()->toJson();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOfferURLs()
    {
        $offerURLs = new URLs(Company::loadFromSession());
        $urls = $offerURLs->getOfferUrls()->fetchAll(\PDO::FETCH_ASSOC);

        return view('offer.urls', compact('urls'));
    }

    /**
     * Request an offer.
     * @param $id
     * @return JsonResponse
     */
    public function requestOffer($id)
    {
        $result = \LeadMax\TrackYourStats\Offer\RepHasOffer::requestOffer($id,
            \LeadMax\TrackYourStats\System\Session::userID());
        return JsonResponse::create($result);
    }

    /**
     * Duplicate an offer.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dupe($id)
    {
        if (\LeadMax\TrackYourStats\Offer\Offer::duplicateOffer($id)) {
            $message = 'Success!';
        } else {
            $message = 'Oh noes!';
        }

        return back()->with(compact('message'));
    }

    /**
     * Delete an offer.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        \LeadMax\TrackYourStats\Offer\Offer::deleteOffer($id);

        return back();
    }

    /**
     * Show the form to mass assign offers.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createMassAssign()
    {
        $users = User::myUsers()->withRole(request('role', 3))->get();

	    $offers = \LeadMax\TrackYourStats\System\Session::user()->offers()->where('status',1)->get();

        return view('offer.mass-assign', compact('users', 'offers'));
    }

    /**
     * Mass assign offers to affiliates or managers.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeMassAssign(Request $request)
    {
        $this->validate($request, [
            'users' => 'required|array',
            'offers' => 'required|array'
        ]);
        \LeadMax\TrackYourStats\Offer\RepHasOffer::massAssignUsers($request->post('users'), $request->post('offers'),
            request('role', 3));

        if (request()->has("updatePayouts")) {
            \LeadMax\TrackYourStats\Offer\RepHasOffer::massUpdateOfferPayouts($request->post('offers'));
        }

        return back()->with('message', 'Success!');
    }
}