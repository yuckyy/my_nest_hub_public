<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="{{ url('/') }}/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/fontawesome/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')

    <link rel="icon" href="{{ url('/') }}/favicon.png" sizes="32x32"/>
    <link rel="icon" href="{{ url('/') }}/favicon.png" sizes="192x192"/>
    <link rel="apple-touch-icon-precomposed" href="/favicon.png"/>

    <title>MYNESTHUB</title>

    @include('layouts.partials.header_tags')
</head>
<body class="d-flex flex-column h-100">
<div class="preloader">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
         style="margin: auto; background: rgba(255, 255, 255, 0) none repeat scroll 0% 0%; display: block; shape-rendering: auto;"
         width="50px" height="50px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
        <g transform="translate(50 50)">
            <g ng-attr-transform="scale(0.8300000000000001)">
                <g transform="translate(-50 -50)">
                    <path fill="#234375" stroke="#234375" stroke-width="0"
                          d="M50,14c19.85,0,36,16.15,36,36S69.85,86,50,86S14,69.85,14,50S30.15,14,50,14 M50,10c-22.091,0-40,17.909-40,40 s17.909,40,40,40s40-17.909,40-40S72.091,10,50,10L50,10z"></path>
                    <path fill="#dc3737"
                          d="M52.78,42.506c-0.247-0.092-0.415-0.329-0.428-0.603L52.269,40l-0.931-21.225C51.304,18.06,50.716,17.5,50,17.5 s-1.303,0.56-1.338,1.277L47.731,40l-0.083,1.901c-0.013,0.276-0.181,0.513-0.428,0.604c-0.075,0.028-0.146,0.063-0.22,0.093V44h6 v-1.392C52.925,42.577,52.857,42.535,52.78,42.506z">
                        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite"
                                          values="0 50 50;360 50 50" keyTimes="0;1"
                                          dur="1.1363636363636362s"></animateTransform>
                    </path>
                    <path fill="#dc3737"
                          d="M58.001,48.362c-0.634-3.244-3.251-5.812-6.514-6.391c-3.846-0.681-7.565,1.35-9.034,4.941 c-0.176,0.432-0.564,0.717-1.013,0.744l-15.149,0.97c-0.72,0.043-1.285,0.642-1.285,1.383c0,0.722,0.564,1.321,1.283,1.363 l15.153,0.971c0.447,0.027,0.834,0.312,1.011,0.744c1.261,3.081,4.223,5.073,7.547,5.073c2.447,0,4.744-1.084,6.301-2.975 C57.858,53.296,58.478,50.808,58.001,48.362z M50,53.06c-1.688,0-3.06-1.373-3.06-3.06s1.373-3.06,3.06-3.06s3.06,1.373,3.06,3.06 S51.688,53.06,50,53.06z">
                        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite"
                                          values="0 50 50;360 50 50" keyTimes="0;1"
                                          dur="4.545454545454545s"></animateTransform>
                    </path>
                </g>
            </g>
        </g>
    </svg>
</div>

<nav class="mainNav navbar navbar-expand-md navbar-light bg-light fixed-top p-1 pl-md-0 pr-md-0">
    <div class="container-fluid pl-md-5 pr-md-5">
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar"
                aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon-bar top-bar"></span>
            <span class="icon-bar middle-bar"></span>
            <span class="icon-bar bottom-bar"></span>
        </button>

        <a class="navbar-brand" href="#">
            <img src="{{ url('/') }}/images/logo.png" height="40" alt="">
        </a>

        <div class="publicTopRightButtons">
            <div class="d-none d-md-block">
                <a class="btn btn-light btn-sm mr-2" href="{{ route('register') }}"><i
                        class="fal fa-user-plus mr-1"></i> Register</a>
                <a class="btn btn-light btn-sm" href="{{ route('login') }}"><i class="fal fa-sign-in mr-1"></i>
                    Login</a>
            </div>
        </div>

    </div>
</nav>
<nav class="sidebar collapse" id="sidebar">
    <div class="sidebarSticky">
        <div class="list-group list-group-flush">
            <a href="{{ route('register') }}"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                Register <i class="fal fa-user-plus mr-1"></i></i>
            </a>
            <a href="{{ route('login') }}"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                Login <i class="fal fa-sign-in mr-1"></i></i>
            </a>
        </div>
    </div>
</nav>

<main role="main" class="mainContent flex-shrink-0 pl-0">
    @yield('content')
</main>

<footer class="footer mt-auto border-top">
    <div class="container-fluid">

        <div
            class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 ml-2 mr-2">
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="https://www.facebook.com/MYNESTHUB" class="h5 mb-0 text-secondary mr-3" target="_blank"><i
                        class="fab fa-facebook-square" aria-hidden="true"></i></a>
                <a href="https://www.linkedin.com/company/MYNESTHUB-software/" class="h5 mb-0 text-secondary"
                   target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="http://MYNESTHUB.com/contact.html" target="_blank" class="btn btn-light btn-sm mr-3"><i
                        class="fas fa-phone-volume mr-1"></i> Contact Us</a>
                <a href="javascript:void(0);" id="popchat" class="btn btn-light btn-sm"><i
                        class="fas fa-headset mr-1"></i> Support Chat</a>
            </div>
        </div>

    </div>
</footer>
<script src="{{ url('/') }}/vendor/jquery-3.3.1.min.js"></script>
<script src="{{ url('/') }}/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    function pageInitilize() {
        if ($(window).width() > 768) {
            jQuery('#sidebar').hover(function () {
                jQuery(this).addClass('show');
            }, function () {
                jQuery(this).removeClass('show');
            });
            var boxToFix = $('#bTofix');
            boxToFix.css('max-width', 'none');
            boxToFix.css('max-width', (boxToFix.width()) + 'px');
        } else {
            $(".preloader").hide();
        }
    }

    $(document).ready(function () {
        pageInitilize();
        $(window).resize(function () {
            pageInitilize();
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        $(".preloader").fadeOut("slow");
    });
</script>
@include('layouts.partials.chat')
@yield('scripts')
@yield('scripts_in_modules')
</body>
</html>
