@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route("maintenance") }}">Maintenance</a>
    </div>
    <div class="container-fluid">

        <div class="filterMaintenanceBox container-fluid">

            <div class="d-block d-sm-block d-xl-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                @if (empty(Request::get('archived')))
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Maintenance</h1>
                    <span class="badge badge-dark align-top ticketTotalCounter">{{ $countWithoutFilter }} total</span>
                </div>

                <form method="get" id="maintenanceFilterForm" action="{{ route('maintenance/list-view') }}">
                    <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                        <a href="{{ route('maintenance') }}" data-toggle="tooltip" data-placement="top" title="" class="btn btn-sm btn-light mr-3 buttonWithBigIcon" type="button" data-original-title="Panel view">
                            <i class="fal fa-columns"></i>
                        </a>
                        <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                            <select name="status_id" class="custom-select custom-select-sm">
                                <option value="">All Statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" @if($status->id == Request::get('status_id')) selected="selected" @endif >{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                            <select name="priority_id" class="custom-select custom-select-sm">
                                <option value="">All Priority</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}" @if($priority->id == Request::get('priority_id')) selected="selected" @endif >{{ $priority->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mr-3">
                            {{--<select class="custom-select custom-select-sm" name="unit_id" style="max-width: 200px;">
                                <option value="">All Properties/Units</option>
                                @foreach ($units as $unit)
                                    <option data-tokens="{{ $unit->property->address }}/{{ $unit->name }}"
                                            value="{{ $unit->id }}"
                                            @if($unit->id == Request::get('unit_id')) selected="selected" @endif >{{ $unit->property->address }}, {{ $unit->name }}</option>
                                @endforeach
                            </select>--}}
                            <div class="selectpickerBox form-border rounded">
                                <select name="property_id_unit_id" class="selectpicker form-control form-control-sm" data-live-search="true">
                                    <option value="">All Properties/Units</option>
                                    @foreach ($properties_units as $unit)
                                        <option data-tokens="{{ $unit->property_address }}/{{ $unit->unit_name ?? "" }}"
                                                value="{{ $unit->property_id }}_{{ $unit->unit_id }}"
                                                @if($unit->property_id . "_" . $unit->unit_id == Request::get('property_id_unit_id')) selected="selected" @endif >{{ $unit->property_address }} {{ $unit->unit_name ? ", " . $unit->unit_name : " (All Units)" }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- keep for landlord -->
                        <!--<div class="input-group input-group-sm mr-0 mr-sm-3">
                            <input type="text" class="form-control" placeholder="Search by name" aria-label="Search by name" aria-describedby="button-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="button-addon2"><i class="fal fa-times"></i></button>
                            </div>
                        </div>-->

                        <a href="{{ route('maintenance/list-view') }}" class="btn btn-sm btn-primary mr-4 d-none d-lg-inline-block"  data-toggle="tooltip" data-placement="top" title="Reset Filters">
                            <i class="fal fa-times"></i>
                        </a>

                        <a href="{{ route('maintenance/list-view', ["archived" => 1]) }}" class="btn btn-outline-secondary btn-sm mr-3"><i class="fal fa-file-archive mr-1"></i> View Archive</a>
                        <a href="#" data-toggle="modal" data-target="#newTicketModal" data-backdrop="static" data-keyboard="false" class="btn btn-primary btn-sm"><i class="fal fa-plus-circle mr-1"></i> Add New</a>
                    </div>
                </form>
                @else
                    <div class="text-center text-sm-left">
                        <h1 class="h2 d-inline-block">Archived Maintenance Requests</h1>
                        <span class="badge badge-dark align-top ticketTotalCounter">{{ $countWithoutFilter }} total</span>
                    </div>
                    <a href="{{ route('maintenance') }}" class="btn btn-cancel btn-sm"><i class="fal fa-times mr-1"></i> Exit Archive</a>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table noWrapHeader">
                    <thead>
                        <tr>
                            <th>{!! sortableColumn('#', 'maintenance_requests.id', 'maintenance/list-view') !!}</th>
                            <th>{!! sortableColumn('Priority', 'maintenance_requests.priority_id', 'maintenance/list-view') !!}</th>
                            <th>{!! sortableColumn('Submitted at', 'maintenance_requests.created_at', 'maintenance/list-view') !!}</th>
                            {{--<th>Updated at</th>--}}
                            <th>{!! sortableColumn('Property/Unit', 'properties.address', 'maintenance/list-view') !!}</th>
                            {{--<th>Tenant name</th>--}}
                            {{--<th>Tenant contact</th>--}}
                            <th style="width:30%">{!! sortableColumn('Name/Description', 'maintenance_requests.name', 'maintenance/list-view') !!}</th>
                            <th>{!! sortableColumn('Status', 'maintenance_requests.status_id', 'maintenance/list-view') !!}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($maintenanceRequests as $maintenanceRequest)
                            <tr>
                                <td><strong>{{ $maintenanceRequest->id }}</strong></td>
                                <td><strong class="text-{{ $maintenanceRequest->color() }}">{{ $maintenanceRequest->priority->name }}</strong></td>
                                <td>{{ Carbon\Carbon::parse($maintenanceRequest->created_at)->format("m/d/Y") }}</td>
                                {{--<td>{{ Carbon\Carbon::parse($maintenanceRequest->updated_at)->format("m/d/Y") }}</td>--}}
                                <td>{{ $maintenanceRequest->unit->property->full_address }}, {{ $maintenanceRequest->unit->name }}</td>
                                {{--<td>{{ isset($maintenanceRequest->unit->leases[0]) ? $maintenanceRequest->unit->leases[0]->firstname . ' ' . $maintenanceRequest->unit->leases[0]->lastname : 'no leases' }}</td>--}}
                                {{--<td>{{ isset($maintenanceRequest->unit->leases[0]) ? $maintenanceRequest->unit->leases[0]->email : 'no leases' }}</td>--}}
                                <td><strong>{{ $maintenanceRequest->name }}</strong> {{ $maintenanceRequest->truncated_description }}</td>
                                <td>
                                    @if (empty($maintenanceRequest->archived))
                                        <select name="status_id" class="updateStatusControl custom-select custom-select-sm" data-id="{{ $maintenanceRequest->id }}" style="width:120px">
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->name }}" @if($status->id == $maintenanceRequest->status_id)) selected="selected" @endif >{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $maintenanceRequest->status->name }}
                                    @endif
                                </td>
                                <td>
                                    @if (Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                        <span data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="{{ $maintenanceRequest->id }}" data-record-title="{{ '#' . $maintenanceRequest->id . " - " . $maintenanceRequest->name }}" >
                                            <button  class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Ticket"><i class="fal fa-trash-alt"></i></button>
                                        </span>
                                        @if (empty($maintenanceRequest->archived))
                                            <span data-toggle="modal" data-target="#confirmArchiveModal" data-record-id="{{ $maintenanceRequest->id }}" data-record-title="{{ '#' . $maintenanceRequest->id . " - " . $maintenanceRequest->name }}" >
                                                <button class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Archive Ticket"><i class="fal fa-file-archive"></i></button>
                                            </span>
                                        @else
                                            <span data-toggle="modal" data-target="#confirmUnArchiveModal" data-record-id="{{ $maintenanceRequest->id }}" data-record-title="{{ '#' . $maintenanceRequest->id . " - " . $maintenanceRequest->name }}" >
                                                <button class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Unarchive Ticket"><i class="fal fa-box-open"></i></button>
                                            </span>
                                        @endif
                                    @endif
                                    <span data-toggle="modal" class="vacant-check-click"data-target="#detailsModal" data-property-address="{{ $maintenanceRequest->property_address }}" data-profirstname="{{$maintenanceRequest->service_pro_company_name }}{{$maintenanceRequest->service_pro_first_name }} {{$maintenanceRequest->service_pro_last_name }} {{$maintenanceRequest->service_pro_middle_name }}" data-vacant="{{ $maintenanceRequest->deleted_at }}"  data-record-id="{{ $maintenanceRequest->id }}" data-color="{{ $maintenanceRequest->color() }}" data-record-title="{{ '#' . $maintenanceRequest->id . ", " . date('m/d/Y', strtotime($maintenanceRequest->created_at)) }}">
                                        <button class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Details"><i class="fal fa-eye"></i></button>
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>

        <div class="container-fluid">
            @if($maintenanceRequests instanceof \Illuminate\Pagination\LengthAwarePaginator )
                <nav aria-label="Page navigation">
                    @php
                        $appends = [];
                        if (!empty(Request::get('status_id'))) $appends['status_id'] = Request::get('status_id');
                        if (!empty(Request::get('priority_id'))) $appends['priority_id'] = Request::get('priority_id');
                        if (!empty(Request::get('property_id_unit_id'))) $appends['property_id_unit_id'] = Request::get('property_id_unit_id');
                        if (!empty(Request::get('column'))) $appends['column'] = Request::get('column');
                        if (!empty(Request::get('order'))) $appends['order'] = Request::get('order');
                    @endphp
                    @if (!empty($appends))
                        {{ $maintenanceRequests->appends($appends)->onEachSide(1)->render() }}
                    @else
                        {{ $maintenanceRequests->onEachSide(1)->render('vendor.pagination.custom') }}
                    @endif
                </nav>
            @else
                {{--<div class="propCardWrap">
                    <p class="alert alert-warning">
                        @if ($applicationsCountWithoutFilter)
                            There are no applications that matching the filter.
                        @else
                            You didn't have any maintenance request yet.
                        @endif
                    </p>
                </div>--}}
            @endif
        </div>

    </div>

    @include('maintenance.modals-partial')

    <!-- STATUS CHANGED -->
    <div class="modal fade" id="messageStatusChanged" tabindex="-1" role="dialog" aria-labelledby="messageStatusChangedTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageStatusChangedTitle">Status Changed</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div><strong id="statusChangedTitle"></strong> status changed to <strong id="statusChangedText"></strong></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- UNARCHIVE RECORD -->
    <div class="modal fade" id="confirmUnArchiveModal" tabindex="-1" role="dialog" aria-labelledby="confirmUnArchiveModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUnArchiveModalTitle">Confirm Unarchive</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div>Are you sure you would like to unarchive <b><i class="title"></i></b>?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary btn-ok"><i class="fal fa-box-open mr-1"></i> Unarchive</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        jQuery( document ).ready(function($) {
            $('#maintenanceFilterForm').find('select').change(function(){
                $('#maintenanceFilterForm').submit();
            });

            $('.updateStatusControl').change(function(){
                var form_data = new FormData();
                form_data.append('_token', '{{ csrf_token() }}');
                form_data.append('id', $(this).data('id'));
                form_data.append('status', $(this).val());
                $(".preloader").fadeIn("fast");
                $.ajax({
                    url: '{{ route('ajax_update_maintenance_status') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $(".preloader").fadeOut("fast");
                        $('#messageStatusChanged').modal('show');
                        $('#statusChangedTitle').text('#' + response.maintenanceRequest.id + ' - ' + response.maintenanceRequest.name);
                        $('#statusChangedText').text(response.status);
                        setTimeout(function() {$('#messageStatusChanged').modal('hide');}, 4000);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            // ARCHIVE RECORD
            $('#confirmUnArchiveModal').on('click', '.btn-ok', function(e) {
                var id = $(this).data('record-id');

                var form_data = new FormData();
                form_data.append("record_id", id); //record_id
                form_data.append("_token", '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('ajax_maintenance_unarchive') }}', // point to server-side PHP script
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        window.location.reload(true);
                        console.log(response);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });
            $('#confirmUnArchiveModal').on('show.bs.modal', function(event) {
                var t = $(event.relatedTarget);
                $(this).find('.title').text(t.data('record-title'));
                $(this).find('.btn-ok').data('record-id', t.data('record-id'));
            });

        });
    </script>
    <script>
        jQuery( document ).ready(function($) {
            $('#newTicketModal').on('show.bs.modal', function(event) {
                const form = document.getElementById("maintenanceForm");
                form.classList.remove('was-validated');
                $(".invalid-feedback-select").hide();
            });

            $('#newTicketModalSumbit').on('click', function (e) {
                e.preventDefault();

                const name = $('#newTicketModalName').val();
                const description = $('#newTicketModalDescription').val();
                const priority_id = $('#newTicketModalPriority').val();
                const unit_id = $('#newTicketModalProperty').val();
                const maintenanceRequestId = $('#maintenanceRequestId').val();

                const form = document.getElementById("maintenanceForm");
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();

                    $("[required]:invalid").each(function () {
                        var feedback_element = $(this).next(".invalid-feedback");
                        feedback_element.text(feedback_element.data("fieldname") ? "Field " + feedback_element.data("fieldname") + " is required" : "This field is required");
                        var feedback_element_select = $(".invalid-feedback-select");
                        feedback_element_select.text("This field is required");
                    });
                    $("#newTicketModalProperty[required]:invalid").each(function () {
                        $(".invalid-feedback-select").show().text("This field is required");
                    });

                    form.classList.add('was-validated');
                    return 0;
                }
                form.classList.remove('was-validated');

                let form_data = new FormData();

                form_data.append('name', name);
                form_data.append('description', description);
                form_data.append('priority_id', priority_id);
                form_data.append('unit_id', unit_id);
                form_data.append('maintenance_request_id', maintenanceRequestId);
                form_data.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('ajax_maintenance_add') }}', // point to server-side PHP script
                    dataType: 'html', // what to expect back from the PHP script
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $("#maintenanceRequestId").val('');
                        $('#maintenanceFilterForm').submit();
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

        });
    </script>

    @include('maintenance.js-modals-partial')

@endsection
