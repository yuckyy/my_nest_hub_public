@extends('layouts.app')

@section('content')
    {{--

     TODO
     This file may not be in use
     Not sure...

     --}}


    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Pricing</a>
    </div>
    <div class="container-fluid">

        <div class="container-fluid">
            <h1 class="h2 pt-3 pb-1 pl-1">Update Your Plan</h1>
        </div>
        <div class="container-fluid">

            <div class="pl-1">
                <p>We provide an innovative and intuitive property management solution to simplify your life! If you are
                    a landlord, property manager or tenant – our online software will save you time, stress, and money.
                    Messy spreadsheets are out. No more hard calculations. We have left all of this behind and built the
                    tool you need for your every day business.</p>
            </div>

            <div class="row mega-price-table">
                @foreach ($plansToShow as $plan)
                    <div class="col-xl-3 col-md-6 block pb-4">
                        <div class="pricing">

                            <div class="pricing-head">
                                <h3><span class="mr-1">{!! planIcon($plan) !!}</span> {{ $plan->name }}</h3>
                            </div>

                            @if ($plan->price)
                                <h4>
                                    <small>$</small>{{ intval($plan->price) }}
                                    <sup>.{{ explode('.',$plan->price)[1] }}</sup>
                                    <div class="per-month">{{ $plan->period ? 'per '.$plan->period : '' }}</div>
                                </h4>
                            @else
                                <a href="http://MYNESTHUB.com/contact.html" class="h4place" target="_blank">
                                    <!-- price -->
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
                                            <div class="h5 mb-0" style="color:#dc3737">Free 14 days trial</div>
                                        @else
                                            <i class="fa fa-minus text-muted"></i>
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
                                            <span class="ml-1">{{ $subscriptionOptions[$i]->name }}</span>
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
                                    <a href="{{ route('dashboard/subscribe',['plan_id' => $plan->id]) }}"
                                       class="btn btn-primary d-block"><i class="fal fa-plus-circle mr-1"></i> Upgrade
                                        Now</a>
                                @else
                                    <a href="{{ route('dashboard/subscribe',['plan_id' => $plan->id]) }}"
                                       class="btn btn-primary d-block"><i class="fal fa-minus-circle mr-1"></i>
                                        Subscribe Now</a>
                                @endif
                            @else
                                <a href="http://MYNESTHUB.com/contact.html" target="_blank"
                                   class="btn btn-primary d-block"><i class="fas fa-phone-volume mr-1"></i> Request a
                                    quote</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>


        <div class="container-fluid">
            <h2 class="h2 pt-3 pb-1 pl-1">Addons</h2>
        </div>
        <div class="container-fluid">

            <div class="row">
                @foreach ($addons as $addon)
                    <div class="col-md-6">
                        <div class="card propertyForm mb-4">
                            <div class="card-header">
                                <i class="fa fa-puzzle-piece text-secondary mr-1"></i> {{ $addon->name }}
                                @if (Auth::user()->hasAddon($addon->id))
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </div>
                            <div class="card-body bg-light">
                                <p class="card-text">{{ $addon->description }}</p>
                                @if (Auth::user()->hasAddon($addon->id))
                                    <a href="#" class="btn btn-cancel btn-sm">
                                        <i class="fal fa-times mr-1"></i> Cancel Subscription
                                    </a>
                                @else
                                    <a href="#" class="btn btn-primary btn-sm">
                                        <i class="fal fa-cart-plus mr-1"></i> Buy Addon
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            //...
        });
    </script>
@endsection
