@extends('layouts.master')
@section('content')
    <!--right_panel-->
    <div class="right_panel">
        <div class="white_box_outer">
            <div class="heading_holder value_span9"><span class="lft">Edit Offer {{$offer->idoffer}}</span></div>
            <div class="white_box value_span8">

                <form action="{{route('offers.update', $offer->idoffer)}}" method="post" id="form"
                      enctype="multipart/form-data">
                    {{csrf_field()}}
                    {{method_field('PATCH')}}
                    <div class="left_con01">
                        <p>
                            <label class="value_span9">Name</label>
                            <input type="text" class="form-control" name="offer_name" maxlength="155"
                                   value="{{$offer->offer_name}}" id="offer_name"/>
                        </p>

                        <p>
                            <label for="visibility" class="value_span9">Visibility</label>
                            <select name="visibility" id="visibility">
                                <option value="1" {{$offer->is_public == 1 ? "selected " : ""}}>Public</option>
                                <option value="0" {{$offer->is_public == 0 ? "selected" : "" }}>Private</option>
                                <option value="2" {{$offer->is_public == 2 ? "selected" : "" }}>Requestable</option>
                            </select>
                        </p>


                        @if(\LeadMax\TrackYourStats\System\Session::user()->getRole() == \App\Privilege::ROLE_GOD)
                            <p>
                                <label class="value_span9">Advertiser</label>
                                <select name="campaign">
                                    @foreach($campaigns as $campaign)
                                        <option @if ($campaign->id == $offer->campaign_id) selected
                                                @endif value="{{$campaign->id}}">{{$campaign->name}}</option>
                                    @endforeach
                                </select>
                            </p>
                        @endif
                        <p>
                            <label class="value_span9">Status</label>
                            <select class="form-control input-sm" onchange="areYouSure(this);" id="status"
                                    name="status">
                                <option @if($offer->status == 1) selected
                                        @endif @if(\LeadMax\TrackYourStats\System\Session::user()->getRole() != \App\Privilege::ROLE_GOD) disabled
                                        @endif value="1">Active
                                </option>
                                <option @if($offer->status == 0) selected
                                        @endif @if(\LeadMax\TrackYourStats\System\Session::user()->getRole() != \App\Privilege::ROLE_GOD) disabled
                                        @endif value="0">Disabled
                                </option>
                            </select>
                        </p>
                        <p>
                            <label class="value_span9">Type</label>
                            <select class="form-control input-sm " id="offer_type" name="offer_type">
                                <option value="{{ \App\Offer::TYPE_CPA }}"
                                        @if($offer->offer_type == \App\Offer::TYPE_CPA) selected @endif >CPA
                                </option>
                                <option value="{{\App\Offer::TYPE_CPC}}"
                                        @if($offer->offer_type == \App\Offer::TYPE_CPC) selected @endif >CPC
                                </option>
                                <option value="{{\App\Offer::TYPE_PENDING_CONVERSION}}"
                                        @if($offer->offer_type == \App\Offer::TYPE_PENDING_CONVERSION) selected @endif>
                                    Pending
                                    Conversion
                                </option>
                            </select>

                        </p>
                        <p>
                            <label class="value_span9">Description</label>
                            <input type="text" class="form-control" name="description" maxlength="555"
                                   value="{{$offer->description}}" id="description"/>
                        </p>
                        <p>
                            <label class="value_span9">URL</label>
                            <input @if (\LeadMax\TrackYourStats\System\Session::userType() != \App\Privilege::ROLE_GOD) disabled
                                   @endif type="text" class="form-control" name="url" maxlength="555"
                                   value="{{$offer->url}}" id="url"/>
                            <span class="small_txt value_span10">The offer URL where traffic will be directed to. The variables below can be used in offer URLs.</span>
                        <p>

                            When building offer url, these values will populate automatically:

                            <span class="small_txt value_span10">AffiliateID: #affid#</span>
                            <span class="small_txt value_span10">Username: #user#</span>
                            <span class="small_txt value_span10">Click ID: #clickid#</span>
                            <span class="small_txt value_span10">Offer ID: #offid#</span>
                            <span class="small_txt value_span10">Manager ID: #manid#</span>
                            <span class="small_txt value_span10">Admin ID: #adminid#</span>
                        </p>
                        <p>
                            When storing values Sub ID 1-5 on incoming clicks, these tags will populate the
                            corresponding
                            values.
                            <span class="small_txt value_span10">Sub ID 1: #sub1#</span>
                            <span class="small_txt value_span10">Sub ID 2: #sub2#</span>
                            <span class="small_txt value_span10">Sub ID 3: #sub3#</span>
                            <span class="small_txt value_span10">Sub ID 4: #sub4#</span>
                            <span class="small_txt value_span10">Sub ID 5: #sub5#</span>
                        </p>
                        <span class="btn_yellow"> <input type="submit" name="button"
                                                         class="value_span6-2 value_span2 value_span1-2"
                                                         value="Update" onclick="return selectAll();"/></span>
                        <span class="btn_yellow" style="margin-left:2%;"> <a onclick="history.go(-1);"
                                                                             class="value_span6-2 value_span2 value_span1-2"
                            >Cancel</a></span>
                    </div>
                    <div class="right_con01">
                        <p>
                            <label for="payout" class="value_span9">Payout</label>
                            <input type="text" class="form-control" name="payout" maxlength="12"
                                   value="{{$offer->payout}}" id="payout"/>
                            <span class="small_txt value_span10">The Amount paid to affiliates per conversion</span></p>
                        <p>
                            <script type="text/javascript">
                                {{"var cap_enabled = " . (!is_null($offer->cap) && $offer->cap->status == 1 ? "true" : "false") . ";"}}
                                $(document).ready(function () {
                                    $('#enable_bonus_offer').change(function () {
                                        $('#enable_bonus_offer').attr('disabled', 'disabled');
                                        if ($('#bonus_offer_div').css('display') === 'none') {
                                            $('#required_sales').removeAttr('disabled');
                                            $('#bonus_offer_div').slideDown('slow', function () {
                                                $('#enable_bonus_offer').removeAttr('disabled');
                                            });
                                        } else {
                                            $('#required_sales').attr('disabled', 'disabled');
                                            $('#bonus_offer_div').slideUp('slow', function () {
                                                $('#enable_bonus_offer').removeAttr('disabled');
                                            });
                                        }
                                    });
                                    @if($offer->bonus && $offer->bonus->active == 1)
                                    @php echo "$('#enable_bonus_offer').click();"; @endphp
                                    @endif
                                    $('#enable_cap').change(function () {
                                        $('#enable_cap').attr('disabled', 'disabled');
                                        $('#enable_cap').attr('disabled', 'disabled');
                                        let capForm = $('#offer_cap_form');
                                        if (capForm.css('display') === 'none') {
                                            $('#cap_type').removeAttr('disabled');
                                            $('#cap_interval').removeAttr('disabled');
                                            $('#interval_cap').removeAttr('disabled');
                                            $('#redirect_offer').removeAttr('disabled');
                                            capForm.slideDown('slow', function () {
                                                $('#enable_cap').removeAttr('disabled');
                                            });
                                        } else {
                                            $('#cap_type').prop('disabled', true);
                                            $('#cap_interval').prop('disabled', true);
                                            $('#cap_num').prop('disabled', true);
                                            $('#redirect_offer').prop('disabled', true);
                                            capForm.slideUp('slow', function () {
                                                $('#enable_cap').removeAttr('disabled');
                                            });
                                        }
                                    });
                                    if (cap_enabled) {
                                        $('#enable_cap').click();
                                    }
                                });
                            </script>
                        <p>
                            <label for="enable_cap" class="value_span9">Offer Cap</label>
                            <input class="fixCheckBox" type="checkbox" id="enable_cap" name="enable_cap"
                                   value="1">Enable
                            Offer Cap
                        <p id="offer_cap_form" style="display:none;">
                            <span class="small_txt value_span10">Cap Type</span>
                            <select id="cap_type" name="cap_type" disabled>
                                <option
                                        @if ($offer->cap && $offer->cap->type == \App\OfferCap::TYPE_CLICKS)   selected
                                        @endif
                                        value="{{\App\OfferCap::TYPE_CLICKS}}">Click
                                </option>
                                <option
                                        @if ($offer->cap && $offer->cap->type == \App\OfferCap::TYPE_CONVERSIONS)  selected
                                        @endif
                                        value="{{\App\OfferCap::TYPE_CONVERSIONS}}">Conversion
                                </option>
                            </select>
                            <span class="small_txt value_span10">Cap Interval</span>
                            <select id="cap_interval" name="cap_interval" disabled>
                                <option @if($offer->cap && $offer->cap->time_interval == \App\OfferCap::INTERVAL_DAILY) selected
                                        @endif value="{{\App\OfferCap::INTERVAL_DAILY}}">Daily
                                </option>
                                <option @if ($offer->cap && $offer->cap->time_interval == \App\OfferCap::INTERVAL_WEEKLY)  selected
                                        @endif
                                        value
                                        ="{{\App\OfferCap::INTERVAL_WEEKLY}}">Weekly
                                </option>
                                <option @if ($offer->cap &&$offer->cap->time_interval == \App\OfferCap::INTERVAL_MONTHLY) selected
                                        @endif
                                        value
                                        ="{{\App\OfferCap::INTERVAL_MONTHLY}}">Monthly
                                </option>
                                <option @if ($offer->cap &&$offer->cap->time_interval == \App\OfferCap::INTERVAL_TOTAL)  selected
                                        @endif
                                        value
                                        ="{{\App\OfferCap::INTERVAL_TOTAL}}">Total
                                </option>
                            </select>
                            <span class="small_txt value_span10">Interval Cap</span>
                            <input type="number" name="interval_cap"
                                   value="{{$offer->cap ? $offer->cap->interval_cap : 0}}"
                                   id="interval_cap" disabled required/>
                            <span class="small_txt value_span10">Offer Redirect on Cap</span>
                            <select name="redirect_offer" id="redirect_offer" disabled required>
                                @foreach($redirectableOffers as $redirectOffer)
                                    @if($offer->cap && $offer->cap->redirect_offer == $redirectOffer->idoffer)
                                        <option selected
                                                value="{{$redirectOffer->idoffer}}">{{$redirectOffer->offer_name}}</option>
                                    @else
                                        <option value="{{$redirectOffer->idoffer}}">{{$redirectOffer->offer_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </p>
                        <p>
                            <label for="enable_bonus_offer" class="value_span9">Bonus Offer</label>
                            <input class="fixCheckBox" type="checkbox" id="enable_bonus_offer"
                                   name="enable_bonus_offer" value="1"> Enable
                        <p id="bonus_offer_div" style="display:none;">
                            <label for="required_sales">Required Sales:</label>
                            <input type="number" name="required_sales" id="required_sales"
                                   value="{{is_null($offer->bonus) ? 0 : $offer->bonus->required_sales}}"
                                   style="width:100px" disabled>
                        </p>
                        <p>
                            <input @if(request('user_type', \App\User::ROLE_AFFILIATE) == \App\User::ROLE_MANAGER) checked
                                   @endif
                                   onchange="window.location = '{{request()->fullUrlWithQuery(array_merge(request()->except('user_type',3), ['user_type' => \App\User::ROLE_MANAGER]))}}';"
                                   type="radio"
                                   name="assignToType" value="man" style="width:2%;"> Managers
                            <input @if(request('user_type', \App\User::ROLE_AFFILIATE) == \App\User::ROLE_AFFILIATE) checked
                                   @endif
                                   onchange="window.location = '{{request()->fullUrlWithQuery((array_merge(request()->except('user_type',3), ['user_type' => \App\User::ROLE_AFFILIATE])))}}'"
                                   type="radio" name="assignToType" value="aff" style="width:2%;">Affiliates

                        </p>
                        <p>
                            <span class="small_txt value_span10">Assigned {{request('user_type', 3) == \App\User::ROLE_AFFILIATE ? "Affiliates": "Managers"}}</span>
                            <select multiple onchange="moveToUnAssign(this)" class="form-control input-sm"
                                    id="assigned"
                                    name="assigned[]">
                                @foreach($assignedUsers as $user)
                                    <option value="{{$user->idrep}}">{{$user->user_name}}</option>
                                @endforeach
                            </select>
                            <input type="text" id="assignedTxtBox" onchange="searchSelectBox(this);" maxlength="25"
                                   placeholder="Search for  {{request('user_type',3) == \App\User::ROLE_AFFILIATE ? "Affiliates": "Managers"}}..."/>
                        </p>
                        <p>
                            <span class="small_txt value_span10">Unassigned {{request('user_type',3) == \App\User::ROLE_AFFILIATE ? "Affiliates": "Managers"}}</span>
                            <select multiple onchange="moveToAssign(this)" class="form-control input-sm"
                                    id="unassigned"
                                    name="unassigned[]">
                                @foreach($unAssignedUsers as $user)
                                    <option value="{{$user->idrep}}">{{$user->user_name}}</option>
                                @endforeach
                            </select>
                            <input type="text" id="unassignedTxtBox" onchange=" searchSelectBox(this);" maxlength="25"
                                   placeholder="Search for  {{request('user_type',3) == \App\User::ROLE_AFFILIATE ? "Affiliates": "Managers"}}..."/>
                        </p>
                        <p style="margin-top:10px;">
                            <label class="value_span9">Offer Timestamp</label>
                            <input type="text" class="form-control" name="offer_timestamp" maxlength="19"
                                   value="{{$offer->offer_timestamp}}" id="offer_timestamp" disabled/>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="{{asset('js/offer.js')}}?v=1"></script>
        <!--right_panel-->
@endsection