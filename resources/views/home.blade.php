@extends('layouts.master')


@section('content')
    <div class = "right_panel">

        <div class = "white_box_outer">
            <div class = "heading_holder">
                <span class = "lft value_span9">My Account</span>
            </div>
            <div class = "clear"></div>
            <div class = "white_box value_span8">

                <div class = "com_acc">
                    <p><span class = "lft value_span9">Name</span><span class = "rt value_span10">{{$firstName}}</span></p>
                    <p><span class = "lft value_span9">E-mail:</span><span class = "rt "><a href = "mailto:{{$email}}">{{$email}}</a></span></p>
                    <p><span class = "lft value_span9">Password</span><span class = "rt value_span10"><a href = "{{$webroot . "aff_update.php?idrep=" . $userId}}">Change Password</a></span></p>

                    @if ($canViewPostback)
                        <p><span class = "lft value_span9">PostBack URL:</span>
                        <p>
                            <span id = "pb1" class = "rt blue_txt\">{{$postBackURL}}</span>
                            <button onclick = "copyToClipboard(getElementById('pb1'));" class = 'btn btn-default btn-sm'><img src = '/images/icons/page_copy.png' alt = 'Copy'></button>
                        </p>
                    @endif


                    <div class = "com_acc">
                        <a class = "btn btn-default" href = "aff_update.php?idrep={{$userId}}"><img src = "/images/icons/user_edit.png" alt = "Edit"> &nbsp;Edit my account</a>
                    </div>
                </div><!-- com_acc -->
            </div><!-- white_box -->
        </div><!-- white_box_outer -->
    </div>
    <!--right_panel-->


@endsection
