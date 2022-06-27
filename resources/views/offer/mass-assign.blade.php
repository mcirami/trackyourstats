@extends('layouts.master')

@section('content')
    <!--right_panel-->
    <div class="right_panel">
        <div class="white_box_outer">
            <div class="heading_holder value_span9"><span class="lft">Mass Assign Offers </span></div>
            <div class="white_box value_span8">

                <form action="/offers/mass-assign?{{http_build_query(request()->all())}}" method="post"
                      id="form"
                      enctype="multipart/form-data">
                    @include('report.options.user-type')

                    {{csrf_field()}}

                    <div class="form-group">
                        <label for="updatePayouts">Update Offers Payouts </label>
                        <input class="fixCheckBox" type="checkbox" name="updatePayouts" value="1">
                    </div>


                    <div class="left_con01" id="users">
                        <a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="checkBoxesInDiv('users')">Check
                            All</a>

                        <a class="btn btn-default btn-sm" href="javascript:void(0);"
                           onclick="unCheckBoxesInDiv('users')">UnCheck
                            All</a>
                        <p>
                            @foreach($users as $user)
                                <label><input class='fixCheckBox' type='checkbox' name='users[]'
                                              value='{{$user->idrep}}'> {{$user->idrep}} {{$user->user_name}} </label>
                            @endforeach
                        </p>
                        <span class="btn_yellow"> <input type="submit" name="button"
                                                         class="value_span6-2 value_span2 value_span1-2"
                                                         value="Assign Users" onclick=""/></span>
                    </div>

                    <div class="right_con01" id="offers">
                        <a class="btn btn-default btn-sm" href="javascript:void(0);"
                           onclick="checkBoxesInDiv('offers')">Check
                            All</a>
                        <a class="btn btn-default btn-sm" href="javascript:void(0);"
                           onclick="unCheckBoxesInDiv('offers')">UnCheck
                            All</a>

                        <p>
                            @foreach($offers as $offer)
                                <label><input class='fixCheckBox' type='checkbox' name='offers[]'
                                              value='{{$offer->idoffer}}'> {{$offer->idoffer}} {{$offer->offer_name}} </label>
                            @endforeach
                        </p>

                    </div>
            </div>


        </div>
    </div>


@endsection

