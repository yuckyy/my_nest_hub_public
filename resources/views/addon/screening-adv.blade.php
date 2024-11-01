@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Request new feature</a>
    </div>
    <div class="container pb-4">
        <div class="row justify-content-md-center">
            <div class="col-lg-8">
                <div class="container">
                    <div class="pt-4 pb-3">
                        <h1 class="h2 text-center text-sm-left">Tenant Screening</h1>
                    </div>
                </div>
                <div class="container">

                        <div class="card propertyForm">
                            <div class="card-header text-primary2">
                                <i class="fa fa-puzzle-piece mr-1"></i> Tenant Screening Service
                                @if (Auth::user()->hasAddon($addon->name))
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </div>

                            <div class="card-body bg-light">
                                {{--}}
                                <p class="card-text">{{ $addon->description }}</p>
                                <p class="card-text"><strong>${{ $addon->price }}</strong></p>
                                {{--}}

                                <p>In order to use this feature, you need to buy an additional Add On: Tenant Screening Service.This add-on is a monthly addition to your membership cost. Benefits of using this feature:</p>
                                <ul>
                                    <li>Unlimited screening with a monthly fee of <strong>${{ $addon->price }}</strong></li>
                                    <li>The additional cost of <strong>$29.99 will</strong> be applied to the tenant for each screening.</li>
                                    <li>Nationwide Criminal Records</li>
                                    <li>Credit Report</li>
                                    <li>Bankruptcies</li>
                                    <li>Foreclosures</li>
                                </ul>
                            </div>
                            <div class="card-footer text-muted d-flex justify-content-between">
                                <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-arrow-left mr-1"></i> Back
                                </a>

                                @if (Auth::user()->hasAddon($addon->name))
                                    <span>You already subscribed to this addon</span>
                                @else
                                    <a href="{{ route('profile/addon',['addon_id' => $addon->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="fal fa-cart-plus mr-1"></i> Buy Addon
                                    </a>
                                @endif
                            </div>
                        </div><!-- /propertyForm -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
