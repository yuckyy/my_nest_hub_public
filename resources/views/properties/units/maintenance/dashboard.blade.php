@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')

    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @include('properties.units.header-partial')
                {{--                <!--@if ($applications->count() == 0)--!>--}}
                {{--                    <!--<a href="{{ route('applications/add', ['unit_id' => $unit]) }}" class="btn btn-primary btn-sm mb-2 ml-lg-auto mr-sm-3"><i class="fal fa-plus-circle mr-1"></i>Create Application</a>--!>--}}
                {{--                    <!--<a href="#" data-toggle="modal" data-target="#inviteModal" class="btn btn-primary btn-sm mb-2"><i class="fal fa-pennant mr-1"></i> Invite Tenant</a>--!>--}}
                {{--                <!--@endif--!>--}}
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="navTabsLeftContainer col-md-3">
                    @include('includes.units.menu')
                </div>
                <div class="navTabsLeftContent col-md-9">
{{--                <div class="container-fluid d-none d-md-block breadCrumbs">--}}
{{--        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route("maintenance") }}">Maintenance</a>--}}
{{--    </div>--}}<div class="card propertyForm">
                        <div class="card-header">
                            <div class="filterMaintenanceBox container-fluid">

                                <div class="d-block d-sm-block d-lg-flex justify-content-between flex-wrap flex-md-nowrap align-items-center ">
                                    <div class="text-center text-sm-left">
                                        <h1 class="h2 d-inline-block">Maintenance</h1>
                                        <span class="badge badge-dark align-top ticketTotalCounter">{{ $countWithoutFilter }} total</span>
                                    </div>
                                    <form method="get" id="maintenanceFilterForm" action="{{ route('properties/units/maintenance',['unit' => $unit]) }}">
                                        <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                                            <a href="{{ route('properties/units/maintenance/list-view',['unit' => $unit]) }}" data-toggle="tooltip" data-placement="top" title="" class="btn btn-sm btn-light mr-3 buttonWithBigIcon" type="button" data-original-title="List view">
                                                <i class="fal fa-th-list"></i>
                                            </a>
                                            <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                                                <select name="priority_id" class="custom-select custom-select-sm">
                                                    <option value="">All Priority</option>
                                                    @foreach ($priorities as $priority)
                                                        <option value="{{ $priority->id }}" @if($priority->id == Request::get('priority_id')) selected="selected" @endif >{{ $priority->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{--                                                <div class="mr-3">--}}
                                            {{--                                                    --}}{{--<select class="custom-select custom-select-sm" name="unit_id" style="max-width: 200px;">--}}
                                            {{--                                                        <option value="">All Properties/Units</option>--}}
                                            {{--                                                        @foreach ($units as $unit)--}}
                                            {{--                                                            <option data-tokens="{{ $unit->property->address }}/{{ $unit->name }}"--}}
                                            {{--                                                                    value="{{ $unit->id }}"--}}
                                            {{--                                                                    @if($unit->id == Request::get('unit_id')) selected="selected" @endif >{{ $unit->property->address }}, {{ $unit->name }}</option>--}}
                                            {{--                                                        @endforeach--}}
                                            {{--                                                    </select>--}}
                                            {{--                                                    <div class="selectpickerBox form-border rounded">--}}
                                            {{--                                                        <select name="property_id_unit_id" class="selectpicker form-control form-control-sm" data-live-search="true">--}}
                                            {{--                                                            <option value="">All Properties/Units</option>--}}
                                            {{--                                                            @foreach ($properties_units as $unit)--}}
                                            {{--                                                                <option data-tokens="{{ $unit->property_address }}/{{ $unit->unit_name ?? "" }}"--}}
                                            {{--                                                                        value="{{ $unit->property_id }}_{{ $unit->unit_id }}"--}}
                                            {{--                                                                        @if($unit->property_id . "_" . $unit->unit_id == Request::get('property_id_unit_id')) selected="selected" @endif >{{ $unit->property_address }} {{ $unit->unit_name ? ", " . $unit->unit_name : " (All Units)" }}</option>--}}
                                            {{--                                                            @endforeach--}}
                                            {{--                                                        </select>--}}
                                            {{--                                                    </div>--}}
                                            {{--                                                </div>--}}

                                            <!-- keep for landlord -->
                                            <!--<div class="input-group input-group-sm mr-0 mr-sm-3">
                                                <input type="text" class="form-control" placeholder="Search by name" aria-label="Search by name" aria-describedby="button-addon2">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button" id="button-addon2"><i class="fal fa-times"></i></button>
                                                </div>
                                            </div>-->

                                            <a href="{{ route('properties/units/maintenance',['unit' => $unit]) }}" class="btn btn-sm btn-primary mr-4 d-none d-lg-inline-block" data-toggle="tooltip" data-placement="top" title="Reset Filters">
                                                <i class="fal fa-times"></i>
                                            </a>
                                            @if($queryArchiveCount > 0)
                                                <a href="{{ route('properties/units/maintenance/list-view', ["unit" => $unit,"archived" => 1]) }}" class="btn btn-outline-secondary btn-sm mr-3"><i class="fal fa-file-archive mr-1"></i> View Archive</a>
                                            @endif
                                            <a href="#" data-toggle="modal" data-target="#newTicketModal" data-backdrop="static" data-keyboard="false" class="mr-3 btn btn-primary btn-sm"><i class="fal fa-plus-circle mr-1"></i> Add New</a>
                                            @if(Auth::user()->isLandlord())
                                                <a href="{{ route('maintenance/service-pro') }}" class="btn btn-primary btn-sm">Service Pro</a>
                                            @endif
                                        </div>
                                    </form>
                                </div>

                            </div>

                        </div>
                    <div class="card-body bg-light ">
                        <div class="container-fluid">

                            @if (Auth::user()->isTenant() || (count(Auth::user()->properties) > 0))


                            @else
                                <div class="p-3">
                                    <div class="text-center text-sm-left pt-1 pb-2">
                                        <h1 class="h2">Maintenance</h1>
                                    </div>

                                    <div class="card border-warning propertyForm">
                                        <div class="card-body text-center alert-warning">
                                            <p class="m-0">You didn't create any properties yet. Press "Add New Property" to create new property.</p>
                                        </div>
                                        <div class="card-footer border-warning text-muted text-center">
                                            <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                                                <i class="fal fa-plus-circle mr-1"></i> Add New Property
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif



                            <div class="container-fluid">
                                <div id="maintenanceCardBox" class="maintenanceCardBox mb-3">
                                    <div class="row pb-4">
                                        <div class="col-md-4">
                                            <div class="card maintenancePad">
                                                <div class="card-header bg-white">
                                                    New (<span class="ticketCounter">0</span><span id="inTotalResolved" class="inTotal d-none"> in Total</span>)
                                                    <a id="viewAllButtonResolved" style="margin: -0.3rem auto;" class="viewAllLink btn btn-sm btn-primary float-sm-right" href="{{ route('properties/units/maintenance/list-view', ['unit' => $unit ],['status_id' => 1 ]) }}">View All</a>
                                                </div>
                                                <div data-status="New" class="card-body bg-light">
                                                    <div class="maintenanceDragBox">
                                                        @foreach ($maintenanceRequestsNew as $maintenanceRequest)
                                                            {!! view('maintenance.maintenance_request', [
                                                                'maintenanceRequest' => $maintenanceRequest
                                                            ]) !!}
                                                        @endforeach
                                                    </div>

                                                    <div class="dragIcon text-center">
                                                        <i class="fal fa-arrows"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card maintenancePad">
                                                <div class="card-header bg-white">
                                                    In Progress (<span class="ticketCounter">0</span><span id="inTotalResolved" class="inTotal d-none"> in Total</span>)
                                                    <a id="viewAllButtonResolved" style="margin: -0.3rem auto;" class="viewAllLink btn btn-sm btn-primary float-sm-right" href="{{ route('maintenance/list-view', ['status_id' => 2 ]) }}">View All</a>
                                                </div>

                                                <div data-status="In Progress" class="card-body bg-light">
                                                    <div class="maintenanceDragBox">
                                                        @foreach ($maintenanceRequestsInProgress as $maintenanceRequest)
                                                            {!! view('maintenance.maintenance_request', [
                                                            'maintenanceRequest' => $maintenanceRequest
                                                            ]) !!}
                                                        @endforeach
                                                    </div>

                                                    <div class="dragIcon text-center">
                                                        <i class="fal fa-arrows"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card maintenancePad">
                                                <div class="card-header bg-white">
                                                    Resolved (<span class="ticketCounter">0</span><span id="inTotalResolved" class="inTotal d-none"> in Total</span>)
                                                    <a id="viewAllButtonResolved" style="margin: -0.3rem auto;" class="viewAllLink btn btn-sm btn-primary float-sm-right" href="{{ route('maintenance/list-view', ['status_id' => 3 ]) }}">View All</a>
                                                </div>

                                                <div data-status="Resolved" class="card-body bg-light">
                                                    <div class="maintenanceDragBox">
                                                        @foreach ($maintenanceRequestsResolved as $maintenanceRequest)
                                                            {!! view('maintenance.maintenance_request', [
                                                            'maintenanceRequest' => $maintenanceRequest
                                                            ]) !!}
                                                        @endforeach
                                                    </div>

                                                    <div class="dragIcon text-center">
                                                        <i class="fal fa-arrows"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div><!-- /applicationsCardBox -->


                            </div>

                        </div>
                    </div>
                </div>
            </div>
    @include('properties.units.maintenance.modals-partial')

@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src='{{ url('/') }}/vendor/jquery-sortable.js'></script>
    <script>

        function refreshTotal(){
            // TODO update ticketTotalCounter and maintenanceRequestsCountInMenu based on ajax request
            /*
            let n = 0;

            $(".maintenanceDragBox").each(function(index, element) {
                const countForCurrentStatus = $(this).find('.maintenanceCard').length;

                if (index === 0) { // update total in menu
                    $('#maintenanceRequestsCountInMenu').text(countForCurrentStatus);
                }
                n += countForCurrentStatus;
            });
            $('.ticketTotalCounter').text(n + ' total');
            */
        }
        function dragDataStyleUpdate(){
            $(".maintenanceDragBox").each(function(){
                const maintenanceCards = $(this).find('.maintenanceCard');

                var n = maintenanceCards.length;
                if(n == 0){
                    $(this).parent().parent().addClass('empty');
                } else {
                    $(this).parent().parent().removeClass('empty');
                }
                $(this).parent().parent().find('.card-header').find('.ticketCounter').text(n);
                $(this).parent().parent().find('.card-body').find('.ticketCounter').text(n);

                for (let i = 0; i < n; i++) {
                    if (i < 3) {
                        maintenanceCards[i].style.display = 'block';
                    } else {
                        maintenanceCards[i].style.display = 'none';
                    }
                }

                var ticketHeader = $(this).parent().parent().find('.card-header');
                if (n > 3) {
                    ticketHeader.find('.viewAllLink').show();
                    ticketHeader.find('.inTotal').addClass("d-lg-inline");
                } else {
                    ticketHeader.find('.viewAllLink').hide();
                    ticketHeader.find('.inTotal').removeClass("d-lg-inline");
                }

                refreshTotal();
            });
        }
        $( document ).ready(function($) {

            if($(document).width() > 800){
                dragDataStyleUpdate();
                var group = $(".maintenanceDragBox").sortable({
                    group: 'maintenanceDragBox',
                    containerSelector: 'div.maintenanceDragBox',
                    itemSelector: 'div.maintenanceCard',
                    pullPlaceholder: false,
                    placeholder: '<div style="height: 150px" class="card maintenanceCard callout-default placeholder"></div>',
                    onDragStart: function ($item, container, _super) {
                        var offset = $item.offset(),
                            pointer = container.rootGroup.pointer;

                        adjustment = {
                            left: pointer.left - offset.left,
                            top: pointer.top - offset.top
                        };

                        _super($item, container);
                    },
                    onDrag: function ($item, position) {
                        $item.css({
                            left: position.left - adjustment.left,
                            top: position.top - adjustment.top
                        });
                    },
                    onDrop: function ($item, container, _super) {
                        dragDataStyleUpdate();

                        var form_data = new FormData();
                        form_data.append('_token', '{{ csrf_token() }}');
                        form_data.append('id', $item[0].getAttribute('data-ticketId'));
                        form_data.append('status', container.target[0].parentNode.getAttribute('data-status'));

                        $.ajax({
                            url: '{{ route('ajax_update_maintenance_status') }}',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                            },
                            error: function (response) {
                                console.log(response);
                            }
                        });

                        _super($item, container);
                    }
                });
            }


        });
    </script>
    <script>
        jQuery( document ).ready(function($) {
            $('#maintenanceFilterForm').find('select').change(function(){
                $('#maintenanceFilterForm').submit();
            })
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
                const expense_type = $('#org_div1').val();
                const expense_name = $('#org_div2').children().val();
                const expense_pid = $('#org_div4').val();
                form_data.append('name', name);
                form_data.append('expense_type', expense_type);
                form_data.append('expense_name', expense_name);
                form_data.append('pid', expense_pid);
                form_data.append('description', description);
                form_data.append('priority_id', priority_id);
                form_data.append('unit_id', unit_id);
                form_data.append('maintenance_request_id', maintenanceRequestId);
                form_data.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('properties/units/maintenance/ajax_maintenance_add',['unit' => $unit]) }}', // point to server-side PHP script
                    dataType: 'html', // what to expect back from the PHP script
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $("#maintenanceRequestId").val('');
                        $("#sharedFileList").html('');
                        $('#newTicketModalName').val('');
                        $('#newTicketModalDescription').val('');
                        $('#modal-new-ticket-error').css('display', 'none');
                        $('#newTicketModal').modal('hide');

                        const element = document.createElement(null);
                        element.innerHTML = response;

                        $('.maintenanceDragBox')[0].prepend(element); // add in New section

                        dragDataStyleUpdate(); // update totals
                        location.reload();
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

        });
    </script>

    @include('properties.units.maintenance.js-modals-partial')

@endsection
