@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @include('properties.units.header-partial')
                @if (count($leases) == 0)
                    <a href="{{ route('leases/add', ['unit' => $unit->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fal fa-plus-circle mr-1"></i> Add New Lease
                    </a>
                @endif
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="navTabsLeftContainer col-md-3">
                    @include('includes.units.menu')
                </div>
                <div class="navTabsLeftContent col-md-9">
                    <div class="card propertyForm">
                        @if (count($leases) != 0)
                            <div class="card-header">
                                <form method="get" id="application-filter-form" action="{{ route('properties/units/tenants', ['unit' => $unit->id]) }}">

                                    <div class="filterToolbar btn-toolbar mb-2 mb-md-0">

                                        <div class="input-group input-group-sm mr-0 mr-sm-3">
                                            <input type="text" class="form-control" placeholder="Search by name or email" aria-label="Search by name" aria-describedby="button-addon2" id="search-field" name="search" value="{{ $search }}">
                                            <div class="input-group-append">
                                                <a href="{{ route('properties/units/tenants', ['unit' => $unit->id]) }}" class="btn btn-primary" type="button" id="button-addon2" data-toggle="tooltip" data-placement="top" title="Reset Filters"><i class="fal fa-times"></i></a>
                                                {{--<button class="btn btn-primary" type="submit" id="button-addon2">Apply</button>--}}
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                            <div class="tenantCard card-body bg-light emptyUnitCard">
                                @if (count($leases) != 0)
                                    <div class="table-responsive maxResponsiveFix">

                                        <br />

                                        <table class="table noWrapHeader">
                                            <thead>
                                                <tr>
                                                    <th>{!! sortableColumn('First name', 'leases.firstname', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Last name', 'leases.lastname', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Email', 'leases.email', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Phone', 'leases.phone', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Lease start', 'leases.start_date', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Lease end', 'leases.end_date', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Lease amount', 'leases.amount', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                    <th>{!! sortableColumn('Balance', 'balance', 'properties/units/tenants', ['unit' => $unit->id]) !!}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($leases as $lease)
                                                <tr>
                                                    <td>
                                                        <div style="white-space: nowrap">
                                                            @if (empty($lease->deleted_at) && empty($lease->tenantLastLogin()) )
                                                                <div class="d-inline-block">
                                                                    <div class="mr-1 position-relative" data-toggle="tooltip" data-placement="top" title="Tenant has not registered. Resend an Invitation Email." style="margin: -8px 0; top: -3px">
                                                                        <button class="btn btn-light2 btn-sm text-muted" data-toggle="modal" data-target="#confirmResendEmailModal" data-firstname="{{ $lease->firstname }}" data-lastname="{{ $lease->lastname }}" data-lease_id="{{ $lease->id }}">
                                                                            <i class="fas fa-exclamation-triangle text-danger"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <a class="text-dark" href="{{ route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id ]) }}">
                                                                {{ $lease->firstname }}
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id ]) }}">
                                                            {{ $lease->lastname }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id ]) }}">
                                                            {{ $lease->email }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $lease->phone }}</td>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id ]) }}">
                                                            {{ \Carbon\Carbon::parse($lease->start_date)->format("m/d/Y") }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id ]) }}">
                                                            {{ $lease->end_date ? \Carbon\Carbon::parse($lease->end_date)->format("m/d/Y") : '' }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="text-dark" href="{{ route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id ]) }}">
                                                            {{ financeCurrencyFormat($lease->amount) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="{{ $lease->balance >= 0 ? 'text-success' : 'text-danger' }}" href="{{ route('properties/units/payments', ['unit' => $unit->id, 'lease' => $lease->id ]) }}">
                                                            {{ financeCurrencyFormat($lease->balance) }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="alert alert-warning">
                                        @if (empty(Request::get('search')))
                                            You didn't create any lease yet. Press "<a href="{{ route('leases/add', ['unit' => $unit->id]) }}">Add New Lease</a>" to create new lease.
                                        @else
                                            No tenants matching your filter
                                        @endif
                                    </p>
                                @endif
                            </div>
                            @if($leases instanceof \Illuminate\Pagination\LengthAwarePaginator )
                                <div class="card-footer pb-0 pt-3 tenantsFooter">
                                    @php
                                        $appends = [];
                                        if (!empty(Request::get('search'))) $appends['search'] = Request::get('search');
                                        if (!empty(Request::get('column'))) $appends['column'] = Request::get('column');
                                        if (!empty(Request::get('order'))) $appends['order'] = Request::get('order');
                                    @endphp
                                    @if (!empty($appends))
                                        {{ $leases->appends($appends)->onEachSide(1)->render() }}
                                    @else
                                        {{ $leases->onEachSide(1)->render('vendor.pagination.custom') }}
                                    @endif
                                </div>
                            @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resend Email confirmation dialog-->
    <div class="modal fade" id="confirmResendEmailModal" tabindex="-1" role="dialog" aria-labelledby="confirmResendEmailModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmResendEmailModalTitle">Resend an Invitation Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p class="mb-0">Do You want to resend an invitation email to <strong id="modalFirstname"></strong> <strong id="modalLastname"></strong>?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                    <form method="POST" action="{{ route('leases/resend-email') }}">
                        @csrf
                        <input id="leaseId" type="hidden" name="lease" value="">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-paper-plane mr-2"></i> Resend an Invitation Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        jQuery( document ).ready(function($) {
            $('#confirmResendEmailModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                if(button.data('firstname')){
                    $('#modalFirstname').html(button.data('firstname'));
                }
                if(button.data('lastname')){
                    $('#modalLastname').html(button.data('lastname'));
                }
                if(button.data('lease_id')){
                    $('#leaseId').val(button.data('lease_id'));
                }
            });

            if($(".tenantsFooter").find("ul").length === 0){$(".tenantsFooter").hide()}
        });
    </script>
@endsection
