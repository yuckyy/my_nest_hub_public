@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Reports</a>
    </div>
    <div class="container-fluid">

        <div class="container-fluid">
            <div class="pt-3 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2">Reports</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid">

            @if (Auth::user()->isTenant() || (count(Auth::user()->properties) > 0))
            @else
                <div class="card border-warning propertyForm mb-4">
                    <div class="card-body text-center alert-warning">
                        <p class="m-0">You didn't create any properties yet. Press "Add New Property" to create new property.</p>
                    </div>
                    <div class="card-footer border-warning text-muted text-center">
                        <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                            <i class="fal fa-plus-circle mr-1"></i> Add New Property
                        </a>
                    </div>
                </div>
            @endif


            <div class="row">
                <div class="col-xl-6">
                    <div class="card dashboardCard quickButtonsCard mb-4">
                        <div class="card-header">Accounting</div>
                        <div class="card-body bg-light pb-1">
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <a href="{{ route('expenses') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-chart-pie"></i>
                                            </div>
                                            <div class="h6">
                                                Expenses & Profit
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <a href="{{ route('payments') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-receipt"></i>
                                            </div>
                                            <div class="h6">
                                                Transactions
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-4 mb-3">
                                    <a href="{{ route('payments/invoices') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                    <i class="fal fa-comment-alt-dollar"></i>
                                            </div>
                                            <div class="h6">
                                                All Invoices
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <a href="{{ route('payments/invoices',['paid' => 'paid']) }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-file-invoice-dollar"></i>
                                            </div>
                                            <div class="h6">
                                                Paid Invoices
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <a href="{{ route('payments/invoices',['unpaid' => 'unpaid']) }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-file-invoice"></i>
                                            </div>
                                            <div class="h6">
                                                Unpaid Invoices
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-4 mb-3">
                                    <a href="{{ route('payments/invoices',['upcoming' => 'upcoming']) }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-alarm-clock"></i>
                                            </div>
                                            <div class="h6">
                                                Upcoming Invoices
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-xl-6">
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection
