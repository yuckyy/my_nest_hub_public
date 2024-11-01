<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/fontawesome/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/bootstrap/bootstrap_cascader/css/bootstrap-select.min.css"
          rel="stylesheet">

    @yield('styles')

    <link rel="icon" href="{{ url('/') }}/favicon.png" sizes="32x32"/>
    <link rel="icon" href="{{ url('/') }}/favicon.png" sizes="192x192"/>
    <link rel="apple-touch-icon-precomposed" href="/favicon.png"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>myNestHub</title>

    @include('layouts.partials.header_tags')
</head>
<body class="d-flex flex-column h-100">
{{--}}@if (!session('success')){{--}}
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
{{--}}@endif{{--}}
<nav class="mainNav navbar navbar-expand-md navbar-light bg-light fixed-top p-1">
    <div class="container-fluid" id="bTofix">
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar"
                aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon-bar top-bar"></span>
            <span class="icon-bar middle-bar"></span>
            <span class="icon-bar bottom-bar"></span>
        </button>

        <a class="navbar-brand mr-auto" href="{{ url('/') }}">
            <img src="{{ url('/') }}/images/logo.png" height="40" alt="">
        </a>

        <div id="headerNotifications" class="dropdown notificationDropdown">
            @php
                $countUnread = Auth::user()->unreadNotifications->count();
            @endphp
            <button class="btn btn-lg btn-light mr-1 text-secondary shadow-none" id="dropdownMenu2"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fal fa-bell"></i>
                <span class="badge badge-danger @if($countUnread === 0) d-none @endif">{{ $countUnread }}</span>
            </button>
            <div id="notificationsList" class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="dropdownMenu2">
                <div id="notificationsListSizeBox">
                    <h6 class="dropdown-header bg-light">You have {{ $countUnread === 0 ? "no" : $countUnread }} unread
                        notifications</h6>
                    {{--
                    <!-- TODO keep this for a while. remove before go live -->
                    <a href="#" class="dropdown-item" data-notificationid="999999">
                        <div class="dropTitle">
                            <i class="fal fa-file-signature text-success"></i>
                            11/12/2020 Steve Jobs
                        </div>
                        <div class="dropText">applied to Main Street 100, apt 201, Philadelphia, PA</div>
                    </a>
                    <a href="#" class="dropdown-item unread" data-notificationid="999998">
                        <div class="dropTitle">
                            <i class="fal fa-dollar-sign text-danger"></i>
                            11/12/2020 John Doe
                        </div>
                        <div class="dropText">has outstanding balance -$2300</div>
                    </a>
                    <a href="#" class="dropdown-item" data-notificationid="999997">
                        <div class="dropTitle">
                            <i class="fal fa-dollar-sign text-success"></i>
                            11/12/2020 Ivan Kozytskyy
                        </div>
                        <div class="dropText">has paid -$8200</div>
                    </a>
                    <a href="#" class="dropdown-item" data-notificationid="999996">
                        <div class="dropTitle">
                            <i class="fal fa-tools text-danger"></i>
                            11/12/2020 Maintenance Request
                        </div>
                        <div class="dropText">Something wrong on Main Street 100, apt 201, Philadelphia, PA</div>
                    </a>
                    @foreach(Auth::user()->notifications()->limit(4)->get() as $notification)
                        <a href="{{$notification->data['url']}}" class="dropdown-item @if(is_null($notification->read_at)) unread @endif" data-notificationid="{{$notification->notification_id}}">
                            <div class="dropTitle">
                                {!! notificationIcon($notification) !!}
                                {{ $notification->created_at->format("m/d/Y")}} {{ \App\Models\User::find($notification->data['creator_user_id'])->fullName() }}
                            </div>
                            <div class="dropText">
                                @if( isset($notification->data['title']) && $notification->data['title'] )
                                    <strong>{{$notification->data['title']}}</strong>
                                @endif
                                {{$notification->data['description']}}
                            </div>
                        </a>
                    @endforeach
                    --}}
                    <div class="dropLoading"></div>
                </div>
            </div>
        </div>

        <div class="dropdown accDropBox">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                @if (Auth::user())

                    @if (Auth::user()->photoUrl())
                        <img src="{{ Auth::user()->photoUrl() }}">
                    @else
                        <svg viewBox="0 0 50 50" fill="#fff">
                            <rect width="100%" height="100%" fill="#999"/>
                            <text x="50%" y="65%" text-anchor="middle" font-size="24">
                                {{ substr(Auth::user()->name,0,1) }}
                            </text>
                        </svg>
                    @endif
                    <div class="accDropName d-none d-sm-block">
                        {{ Auth::user()->name }}
                        {{ Auth::user()->lastname }}
                        <i>{{ Auth::user()->roles[0]->name }}</i>
                    </div>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                <a class="dropdown-item" type="button" href="{{ route('profile') }}">Account</a>
                @if (!Auth::user()->isAdmin() && !session('adminLoginAsUser'))
                    <a class="dropdown-item" type="button" href="{{ route('profile/finance') }}">Financial Account</a>
                    @if (!Auth::user()->isTenant())
                        <a class="dropdown-item" type="button" href="{{ route('profile/membership') }}">My
                            Membership</a>
                    @endif
                @endif
                <button class="dropdown-item" type="button"
                        onclick="event.preventDefault();$('#logout-form').submit();">
                    Logout <i class="fal fa-sign-out ml-2 text-secondary"></i>
                </button>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                {{-- <button class="dropdown-item" type="button">Logout</button> --}}
            </div>
        </div>
    </div>
</nav>

<nav class="sidebar collapse d-md-block" id="sidebar">
    <div class="sidebarSticky">
        <div class="list-group list-group-flush">


            @if (Auth::user() && (Auth::user()->isLandlord() || Auth::user()->isPropManager()))
                @include('layouts.partials.landlord-menu')
            @elseif (Auth::user() && Auth::user()->isTenant())
                @include('layouts.partials.tenant-menu')
            @else
                @include('layouts.partials.admin-menu')
            @endif

        </div>
    </div>
</nav>

<main role="main" class="mainContent flex-shrink-0">
    @if (session('adminLoginAsUser'))
        <div class="alert alert-warning m-0" role="alert">
            <strong>You are logged in as Admin.</strong>
            <form id="logout-form2" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault();$('#logout-form2').submit();" class="ml-4"
               style="color: #856404">
                <strong>Logout <i class="fa fa-sign-out ml-1" style="color: #856404"></i></strong>
            </a>
        </div>
    @endif
    @yield('content')
</main>

@if(!Auth::user()->hasVerifiedEmail())
    <div class="container-fluid text-right mt-auto pl-md-5">
        <div class="d-inline-block alert alert-primary alert-dismissible fade show mx-3" role="alert">
            {{ __('Signup almost complete!') }}
            {{ __('Please check your inbox for your account activation email.') }}
            {{ __('If you did not receive the email') }}, <a class="alert-link"
                                                             href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
            <button type="button" class="close p-2" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @if (session('resent'))
        <div class="d-none">
            <div id="resendAlert" class="container-fluid">
                <div class="alert alert-success mb-0 mt-3 mr-3 ml-3" role="alert">
                    {{ __('New verification link has been sent to your email address.') }}
                </div>
            </div>
        </div>
    @endif
@endif
<footer class="footer @if(Auth::user()->hasVerifiedEmail()) mt-auto @endif border-top">
    <div class="container-fluid">

        <div
            class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 ml-2 mr-2">
            <div class="btn-toolbar mb-3 mb-md-0">
                <a href="https://www.facebook.com/MYNESTHUB" class="h5 mb-0 text-secondary mr-3" target="_blank"><i
                        class="fab fa-facebook-square" aria-hidden="true"></i></a>
                <a href="https://www.linkedin.com/company/MYNESTHUB-software/" class="h5 mb-0 text-secondary"
                   target="_blank"><i class="fab fa-linkedin"></i></a>

            </div>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="{{ route('dashboard/request-feature') }}" class="btn btn-light btn-sm mr-3"><i
                        class="fal fa-lightbulb-on mr-1"></i> Request New Feature</a>
                <a href="http://MYNESTHUB.com/contact.html" target="_blank" class="btn btn-light btn-sm mr-3"><i
                        class="fas fa-phone-volume mr-1"></i> Contact Us</a>
                <a href="javascript:void(0);" id="popchat" class="btn btn-light btn-sm"><i
                        class="fas fa-headset mr-1"></i> Support Chat</a>
            </div>
        </div>

    </div>
</footer>

<!-- SUBMIT ERROR MODAL -->
<div class="modal fade" id="submitError" tabindex="-1" role="dialog" aria-labelledby="submitErrorTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-center m-auto text-white" id="submitErrorTitle"><i
                        class="fas fa-highlighter mr-3"></i>Please review entered information</h5>
            </div>
            <div class="modal-body text-center bg-light">
                <h6 class="m-0">You'll find more details highlighted</h6>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i
                        class="fal fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="upgradeError" tabindex="-1" role="dialog" aria-labelledby="upgradeErrorTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-center m-auto text-white" id="upgradeErrorTitle"><i
                        class="fal fa-plus-circle mr-3"></i>Upgrade your membership</h5>
            </div>
            <div class="modal-body text-center bg-light">
                <h6 class="m-0">@error('upgradeError') {!! $message !!} @enderror</h6>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i
                        class="fal fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
@if (session('success'))
    <div class="modal fade" id="successMessageModal" tabindex="-1" role="dialog"
         aria-labelledby="successMessageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered @if (session('gif')) modal-lg modal-dialog-scrollable @endif"
             role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-center m-auto text-white" id="successMessageModalTitle"><i
                            class="fas fa-thumbs-up mr-3"></i>Success</h5>
                </div>
                <div class="modal-body text-center bg-light">
                    <h6 class="m-0">{!! session()->get('success') !!}</h6>
                    @if (session('whatsnext'))
                        <div class="alert alert-success mt-3 mb-0" role="alert">
                            <h4 class="alert-heading"><i class="fal fa-walking mr-1"></i> What's Next?</h4>
                            <hr>
                            <p class="mb-0">
                                {!! session()->get('whatsnext') !!}
                            </p>
                        </div>
                    @endif
                    @if (session('gif'))
                        <img class="w-100 mt-3 mb-3" src="{!! session()->get('gif') !!}"/>
                    @endif
                    @if (session('whatsnext_button_text'))
                        <div class="pb-3 pl-3 mt-3 text-center">
                            <a class="btn btn-primary btn-sm"
                               href="{!! session()->get('whatsnext_button_url') !!}">{!! session()->get('whatsnext_button_text') !!}</a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i
                            class="fal fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
@if (session('wait'))
    <div class="modal fade" id="successMessageModal" tabindex="-1" role="dialog"
         aria-labelledby="successMessageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-center m-auto text-white" id="successMessageModalTitle"><i
                            class="fas fa-thumbs-up mr-3"></i>Success</h5>
                </div>
                <div class="modal-body text-center bg-light">
                    <h6 class="m-0">{{ session()->get('wait') }}</h6>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i
                            class="fal fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
{{--}}
@if (session('error'))
    <div class="modal fade" id="errorMessageModal" tabindex="-1" role="dialog" aria-labelledby="errorMessageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-center m-auto text-white" id="errorMessageModalTitle"><i class="fas fa-thumbs-up mr-3"></i>Success</h5>
                </div>
                <div class="modal-body text-center bg-light">
                    <h6 class="m-0">{{ session()->get('error') }}</h6>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
@endif
{{--}}
<div class="modal fade" id="cardCvvModal" tabindex="-1" role="dialog" aria-labelledby="cardCvvTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center bg-light">
                <img src="{{ asset('images/card-cvv.jpeg') }}" alt="Card Cvv">
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i
                        class="fal fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
<script src="{{ url('/') }}/vendor/jquery-3.3.1.min.js"></script>
<script src="{{ url('/') }}/vendor/jquery.mask.min.js"></script>
<script src="{{ url('/') }}/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/') }}/vendor/list.min.js"></script>
<script src="{{ url('/') }}/bootstrap/bootstrap_cascader/js/bootstrap-select.min.js"></script>
<script>
    @if (session('success'))
    jQuery(document).ready(function ($) {
        $('#successMessageModal').modal('show');
        @if( (strlen(session()->get('success')) < 60) && empty(session()->get('whatsnext')) )
        setTimeout(function () {
            $('#successMessageModal').modal('hide');
        }, 4000);
        @endif
    });
    @endif
    @if (session('wait'))
    jQuery(document).ready(function ($) {
        $('#successMessageModal').modal('show');
    });
    $('#successMessageModal').on('hidden.bs.modal', function (e) {
        window.location.reload(true);
    });
    @endif

    @error('upgradeError')
    jQuery(document).ready(function ($) {
        $('#upgradeError').modal('show');
    });
    @enderror
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

    function loadNotifications(last_id) {
        $('.dropLoading').addClass("loading");
        $.ajax({
            url: '{{ route('notifications') }}',
            dataType: 'json',
            cache: false,
            type: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                start_id: last_id
            },
            success: function (data) {
                $('.dropLoading').removeClass("loading");
                if (Array.isArray(data) && data.length) {
                    data.forEach(function callback(el, index, array) {
                        var html = '<a href="' + el.url + '" class="notificationLink dropdown-item' + (el.read_at ? '' : ' unread') + '" data-notificationid="' + el.notification_id + '">' +
                            '<div class="dropTitle">' + el.header + '</div>' +
                            '<div class="dropText">' +
                            (el.title ? '<strong>' + el.title + '</strong> ' : '') + el.description +
                            '</div>' +
                            '</a>';
                        $('.dropLoading').before(html);
                    });
                }
            }
        });
    }

    jQuery(document).ready(function ($) {
        pageInitilize();
        $(window).resize(function () {
            pageInitilize();
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        $(".preloader").fadeOut("slow");

        $('body').on('click', 'a.notificationLink.unread', function (e) {
            e.preventDefault();
            var href = $(this).attr("href");
            var notification_id = $(this).data("notificationid");
            $.ajax({
                url: '{{ route('notifications/mark-as-read') }}',
                cache: false,
                type: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    notification_id: notification_id
                },
                success: function () {
                    window.location.href = href;
                }
            });
        });

        $("#notificationsList").scrollTop();

        $('#headerNotifications').on('show.bs.dropdown', function () {
            if ($('.dropLoading').prev("a").length === 0) {
                loadNotifications(0);
            }
        });

        var last_id = 0;
        $("#notificationsList").on("scroll", function () {
            var scrollHeight = $("#notificationsListSizeBox").height();
            var scrollPos = $("#notificationsList").height() + $("#notificationsList").scrollTop();
            if (scrollHeight - scrollPos - 10 < 0) {
                var scroll_last_id = $('.dropLoading').prev("a").first().data("notificationid");
                if (scroll_last_id !== last_id) {
                    last_id = scroll_last_id;
                    loadNotifications(last_id);
                }
            }
        });
    });
</script>
<script>
    jQuery(document).ready(function ($) {
        jQuery('.grid-datepicker').prop('type', 'date');
        jQuery('.btn-login').click(function (e) {
            e.preventDefault();
            var url = new URL(jQuery(this).attr('href'));
            var id = url.searchParams.get("user");
            console.log(id);
            if (id != null) {
                var userId = jQuery('input[name=user_id]');
                userId.val(id);
                userId.closest('form').submit();
            }
        })
    });
</script>
<script>
    var unsaved = false;
    $(document).ready(function () {

        $(window).bind('beforeunload', function () {
            if (unsaved) {
                return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
            }
        });
        if ($('.checkUnloadByDefoult').length > 0) {
            unsaved = true;
        }
        $(document).on('change', '.checkUnload :input', function () {
            unsaved = true;
        });
        $('form').on('submit', function () {
            unsaved = false;
        });
        $('.btn-cancel').click(function () {
            unsaved = false;
        });

        $(".btn-remove-record").click(function (e) {
            if (!confirm('Are you sure you want to delete this record?')) e.preventDefault();
        });
    });
</script>
@if (session('resent'))
    <script>
        jQuery(document).ready(function ($) {
            $('.breadCrumbs').after($('#resendAlert'));
        });
    </script>
@endif
@include('layouts.partials.chat')
@yield('scripts')
@yield('scripts_in_modules')
@include('layouts.partials.mouseflow')
</body>
</html>
