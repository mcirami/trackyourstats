<!DOCTYPE html>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/ico"
          href="<?PHP echo $webroot . "/" . \LeadMax\TrackYourStats\System\Company::loadFromSession()
                  ->getImgDir() . "/favicon.ico"; ?>"/>
    <link rel="shortcut icon" type="image/ico"
          href="<?PHP echo $webroot . "/" . \LeadMax\TrackYourStats\System\Company::loadFromSession()
                  ->getImgDir() . "/favicon.ico"; ?>"/>
    <link href="{{$webroot}}css/bootstrap.min.css" rel="stylesheet">
    <!--    <link href="css/bootstrap-theme.min.css" rel="stylesheet">-->
    <link href="{{$webroot}}css/animate.css" rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>css/default.css?v=3.0"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>css/tablesorter.default.css"/>

    <link rel="stylesheet" media="screen" type="text/css"
          href="<?php echo $webroot; ?>css/company.php"/>
    <link href="<?php echo $webroot; ?>css/responsive_table.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $webroot; ?>css/drawer.min.css" rel="stylesheet">

    <link href="<?php echo $webroot; ?>css/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $webroot; ?>css/magic.min.css">

    <script type="text/javascript" src="<?php echo $webroot; ?>js/moment.js"></script>
    <script type="text/javascript" src="<?php echo $webroot; ?>js/jquery_2.1.3_jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $webroot; ?>js/jquery-ui.min.js"></script>

    <script type="text/javascript" src="<?php echo $webroot; ?>js/jscolor.min.js"></script>
    <link rel="stylesheet" href="{{$webroot}}css/jquery-ui.min.css"/>
    <script type="text/javascript" src="<?php echo $webroot; ?>js/main.js"></script>
    <script type="text/javascript" src="<?php echo $webroot; ?>js/drawer.js"></script>
    <script type="text/javascript" src="<?php echo $webroot; ?>js/iscroll.min.js"></script>

    <script type="text/javascript" src="<?php echo $webroot; ?>js/tables.js?v=1"></script>
    <script type="text/javascript" src="<?php echo $webroot; ?>js/bootstrap-notify.min.js"></script>


@if(!env('APP_DEBUG') && env('APP_ENV') == 'production')
    <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-127417577-1"></script>
        <script>window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', 'UA-127417577-1');</script>
    @endif


    <title><?php echo \LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?></title>
</head>

<body class="drawer drawer--top value_span7">

<header role="banner" class="mobile_nav">
    <button type="button" class="drawer-toggle drawer-hamburger"><span class="sr-only">toggle navigation</span>
        <span class="drawer-hamburger-icon"></span></button>
    <nav class="drawer-nav" role="navigation">
        <ul class="drawer-menu">
            @php

                $navBar->printNav(true);

                echo " <li>
                            <a class=\"drawer-dropdown-menu-item value_span2-2 value_span3-2 value_span4 value_span5 value_span6
                           \" href=\"/logout\">Logout</a></li>";

            @endphp


        </ul><!-- drawer-menu -->
    </nav>
</header>

<div class="top_sec value_span1">
    <div class="logo">
        <a href="{{$webroot}}"><img
                    src="{{ $webroot.\LeadMax\TrackYourStats\System\Company::loadFromSession()->getImgDir() .  "/logo.png"}}"
                    alt="TRACK YOUR STATS"
                    title="TRACK YOUR STATS"/></a>
    </div>

    <div class="header_right">
        <div class="notification_icon">
            <a id="notif_icon" href="#">

                <?php
                if ($notifications->getInboxCount() != 0)
                {

                ?>
                <div class="notif_count">
                    <p><?= $notifications->getInboxCount(); ?></p>
                </div>
                <?php } ?>
                <img src="<?php echo $webroot; ?>/images/icon-notif.png" alt=""/>
            </a>
            <div id="notification_box">
                <ul>
                    <?php
                    $notifications->printToInbox();
                    ?>

                </ul>
                <div class="link_wrap">
                    <a href="/notifications.php">See All Notifications</a>
                </div>
            </div>
        </div>
        <h3 class="value_span5 username"><?php echo \LeadMax\TrackYourStats\System\Session::userData()->user_name; ?></h3>
        <div class="logout">
            <?php
            echo "<a class=\"value_span4 value_span2-3\" href=\"/logout\">Logout</a>";

            ?>


        </div>
    </div>
</div> <!-- top_sec -->

<div class="panels_wrap">

    <div class="left_panel value_span3">


        <ul>

            <?php
            $navBar->printNav();
            ?>

        </ul>
    </div><!--left_panel-->


    @include('layouts.errors')

    <div id="app">

    </div>
    @yield('content')
    @if(isset($notify))
        <?php \LeadMax\TrackYourStats\System\Notify::info($notify, ''); ?>
    @endif


    @if(isset($message))
        <?php \LeadMax\TrackYourStats\System\Notify::info($message, ''); ?>
    @endif

</div> <!-- close panels_wrap from header -->

@include('layouts.footer')

@yield('footer')

</body>
</html>
