@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Membership</a>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">My Account</h1>
                    <h6 class="text-center text-sm-left pb-3 text-secondary">
                        <a href="{{ route('profile') }}">{{ Auth::user()->fullName() }}</a>
                    </h6>
                </div>
            </div>
        </div>
        <div class="container-fluid unitFormContainer">
            <div class="row">

                @include('includes.user.account-menu',['active' => 'membership'])

                <div class="profileNavTabsLeftContent col-md-9">
                    <div class="card propertyForm propertyFormGeneralInfo">
                        <div class="card-body bg-light">
                            <h1 class="h4 pb-1 pl-1">Membership
                                @if (Auth::user()->activePlan())
                                    <a href="#" data-toggle="modal" data-target="#submitCancel"
                                       class="btn btn-cancel btn-sm float-right">
                                        <i class="fal fa-times mr-1"></i> Cancel Membership
                                    </a>
                                @endif
                            </h1>
                            <div class="pl-1 pb-2">
                                <div class="inRowComment">
                                    <i class="fal fa-info-circle"></i> We provide an innovative and intuitive property
                                    management solution to simplify your life! If you are a landlord, property manager
                                    or tenant – our online software will save you time, stress, and money. Messy
                                    spreadsheets are out. No more hard calculations. We have left all of this behind and
                                    built the tool you need for your every day business.
                                </div>
                                @if(empty(Auth::user()->activePlan()))
                                    <div class="text-danger pb-2">
                                        <strong><i class="fas fa-info-circle text-danger"></i> Your card will not be
                                            charged during trial period.</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="row mega-price-table">
                                @foreach ($plansToShow as $plan)
                                    <div class="col-xl-3 col-md-6 block pb-4">
                                        <div class="pricing">

                                            <div class="pricing-head">
                                                <h3><span class="mr-1">{!! planIcon($plan) !!}</span> {{ $plan->name }}
                                                </h3>
                                            </div>

                                            @if ($plan->price)
                                                <h4>
                                                    <small>$</small>{{ intval($plan->price) }}
                                                    <sup>.{{ explode('.',$plan->price)[1] }}</sup>
                                                    <div
                                                        class="per-month">{{ $plan->period ? 'per '.$plan->period : '' }}</div>
                                                </h4>
                                            @else
                                                <a href="http://MYNESTHUB.com/contact.html" class="h4place"
                                                   target="_blank"><!-- price -->
                                                    <i class="fas fa-phone-volume mr-1"></i> Request a quote
                                                </a>
                                            @endif

                                            <!-- option list -->
                                            @php( $j = 0 )
                                            <ul class="pricing-table list-unstyled">
                                                @if(empty(Auth::user()->activePlan()))
                                                    @php( $j++ )
                                                    <li class="{{ $j % 2 == 0 ? 'alternate' : '' }}">
                                                        @if ($plan->max_units)
                                                            <div class="h5 mb-0" style="color:#dc3737">Free 14 days
                                                                trial
                                                            </div>
                                                        @else
                                                            <div style="line-height:1.50rem"><i
                                                                    class="fa fa-minus text-muted"></i></div>
                                                        @endif
                                                    </li>
                                                @endif

                                                @php( $j++ )
                                                <li class="{{ $j % 2 == 0 ? 'alternate' : '' }}">
                                                    @if ($plan->max_units)
                                                        Up to <strong>{{ $plan->max_units }}</strong> units
                                                    @else
                                                        <strong>Unlimited</strong> units
                                                    @endif
                                                </li>
                                                @for($i = 0; $i < count($subscriptionOptions); $i++)
                                                    @php( $j++ )
                                                    <li class="{{ $j % 2 == 0 ? 'alternate' : '' }}{{ !$plan->hasOption($subscriptionOptions[$i]->id) ? ' d-none d-sm-block' : '' }}">
                                                        @if ($plan->hasOption($subscriptionOptions[$i]->id))
                                                            <i class="fa fa-check text-primary2"></i>
                                                            <span
                                                                class="ml-1">{{ $subscriptionOptions[$i]->name }}</span>
                                                        @else
                                                            <i class="fa fa-minus text-muted"></i>
                                                        @endif
                                                    </li>
                                                @endfor
                                            </ul>
                                            @if ($plan->price)
                                                @if (Auth::user()->activePlan() && Auth::user()->activePlan()->plan_id == $plan->id)
                                                    <span class="btn btn-danger d-block text-center">
                                                    <i class="fal fa-check mr-1"></i> Your Plan
                                                </span>
                                                @elseif (Auth::user()->activePlan() && Auth::user()->activePlan()->plan_id < $plan->id)
                                                    <a href="{{ route('profile/subscribe',['plan_id' => $plan->id]) }}"
                                                       class="btn btn-primary d-block"><i
                                                            class="fal fa-plus-circle mr-1"></i> Upgrade Now</a>
                                                @elseif (Auth::user()->units_count > $plan->max_units)
                                                    <a href="#" data-toggle="modal" data-target="#subscribeError"
                                                       data-name="{{ $plan->name }}"
                                                       data-max_units="{{ $plan->max_units }}"
                                                       class="btn btn-primary d-block"><i
                                                            class="fal fa-minus-circle mr-1"></i> Subscribe Now</a>
                                                @else
                                                    <a href="{{ route('profile/subscribe',['plan_id' => $plan->id]) }}"
                                                       class="btn btn-primary d-block"><i
                                                            class="fal fa-minus-circle mr-1"></i> Subscribe Now</a>
                                                @endif
                                            @else
                                                <a href="http://MYNESTHUB.com/contact.html" target="_blank"
                                                   class="btn btn-primary d-block"><i
                                                        class="fas fa-phone-volume mr-1"></i> Request a quote</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if(empty(Auth::user()->activePlan()))
                                <div class="text-center text-danger pb-3">
                                    <strong><i class="fas fa-info-circle text-danger"></i> Your card will not be charged
                                        during trial period.</strong>
                                </div>
                            @endif

                            <div class="pb-2 text-center">
                                <img class="mb-1" src="{{ url('/') }}/images/visa.svg" height="50" alt="visa">
                                <img class="mb-1" src="{{ url('/') }}/images/mastercard.svg" height="50"
                                     alt="mastercard">
                                <img class="mb-1 mr-2 ml-1" style="margin-top: 2px" src="{{ url('/') }}/images/amex.svg"
                                     height="46" alt="amex">
                                <img class="mb-1" style="margin-top: 2px" src="{{ url('/') }}/images/discover.svg"
                                     height="46" alt="discover">
                            </div>

                        </div>

                        <div class="card-footer">
                            @if (count($addons) != 0)
                                <h2 class="h4 pt-3 pb-3 pl-1">Available Addons</h2>
                                <div class="row">
                                    @foreach ($addons as $addon)
                                        <div class="col-md-6">
                                            <div class="card propertyForm mb-4">
                                                <div class="card-header">
                                                    <i class="fa fa-puzzle-piece text-secondary mr-1"></i> {{ $addon->title }}
                                                    @if (Auth::user()->hasAddon($addon->name))
                                                        <span class="badge badge-success">Active</span>
                                                    @endif
                                                </div>
                                                <div class="card-body bg-light">
                                                    <p class="card-text">{{ $addon->description }}</p>
                                                    <p class="card-text"><strong>${{ $addon->price }}</strong></p>
                                                    @if (Auth::user()->hasAddon($addon->name))
                                                        <a href="#" class="btn btn-cancel btn-sm" data-toggle="modal"
                                                           data-target="#submitAddonCancel"
                                                           data-addon_id="{{ $addon->id }}"
                                                           data-addon_title="{{ $addon->title }}">
                                                            <i class="fal fa-times mr-1"></i> Cancel Subscription
                                                        </a>
                                                    @else
                                                        <a href="{{ route('profile/addon',['addon_id' => $addon->id]) }}"
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fal fa-cart-plus mr-1"></i> Buy Addon
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel subscription confirmation modal -->
    <div class="modal fade" id="submitCancel" tabindex="-1" role="dialog" aria-labelledby="submitErrorTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('profile/cancel-subscription') }}" method="post">
                    @csrf
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-center m-auto text-white" id="submitErrorTitle"><i
                                class="fal fa-times mr-3"></i><cite></cite>Cancel Membership</h5>
                    </div>
                    <div class="modal-body text-center bg-light">
                        <h6 class="m-0">Are you sure you want to cancel your membership? <br>By pressing "Yes", system
                            will delete your account and all associated documents and properties associated with this
                            account. This action can't be undone.</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-cancel mr-auto" data-dismiss="modal"><i
                                class="fal fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fal fa-times mr-1"></i> Yes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel addon confirmation modal -->
    <div class="modal fade" id="submitAddonCancel" tabindex="-1" role="dialog" aria-labelledby="submitAddonCancelTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('profile/addon-cancel') }}" method="post">
                    <input type="hidden" id="addon_id" name="addon_id" value="">
                    @csrf
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-center m-auto text-white" id="submitAddonCancelTitle"><i
                                class="fal fa-times mr-3"></i><cite></cite>Remove Addon</h5>
                    </div>
                    <div class="modal-body text-center bg-light">
                        <h6 class="m-0">Are you sure you want to cancel your subscription to <span
                                id="addon_title"></span>?</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-cancel mr-auto" data-dismiss="modal">No, Keep Me
                            Subscribed
                        </button>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fal fa-times mr-1"></i> Yes,
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subscribeError" tabindex="-1" role="dialog" aria-labelledby="subscribeErrorTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-center m-auto text-white" id="subscribeErrorTitle"><i
                            class="fal fa-times mr-3"></i>You can’t switch</h5>
                </div>
                <div class="modal-body text-center bg-light">
                    <h6 class="m-0">You can’t switch to <span id="plan_name"></span> plan because you own more then
                        <span id="plan_max_units"></span> units.</h6>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i
                            class="fal fa-times mr-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#subscribeError').on('show.bs.modal', function (event) {
                var t = $(event.relatedTarget);
                $('#plan_name').html(t.data('name'));
                $('#plan_max_units').html(t.data('max_units'));
            });

            $('#submitAddonCancel').on('show.bs.modal', function (event) {
                var t = $(event.relatedTarget);
                $('#addon_title').html(t.data('addon_title'));
                $('#addon_id').val(t.data('addon_id'));
            });
        });
    </script>
@endsection
