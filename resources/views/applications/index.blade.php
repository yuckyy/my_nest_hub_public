@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Applications</a>
    </div>
    <div class="container-fluid">

        @if (Auth::user()->isTenant() || (count(Auth::user()->properties) > 0))

            <div class="filterApplicationsBox container-fluid">
                {{--@if (session('success'))
                    <div class="mt-5">
                        <div class="alert alert-success" role="alert">
                            {{ session()->get('success') }}
                        </div>
                    </div>
                @endif--}}
                <div class="d-block d-sm-block d-lg-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">

                    <div class="text-center text-sm-left">
                        <h1 class="h2 d-inline-block">Applications</h1>
                        <span class="badge badge-dark align-top">{{ $applications->total() }} total</span>
                    </div>

                    <form method="get" id="application-filter-form" action="{{ route('applications/filter') }}">

                        <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                            @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                @if($applicationsArchive >= 1)
                                    <button
                                            onclick="window.location='{{ route('applications', ["archived" => 1]) }}'"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="View Archived Applications"
                                            class="btn btn-sm btn-light mr-1 d-inline-block"
                                            type="button"
                                    >
                                        <i class="fal fa-file-archive mr-1"></i>
                                    </button>
                                @endif
                            @endif

                            <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                                <select class="custom-select custom-select-sm" id="applications-field" name="q_applications">
                                    <option value="" hidden>Select Status</option>
                                    <option value="Pending" {{ (!empty(Request::get('q_applications')) && Request::get('q_applications') === "Pending") ? "selected" : ""}}>Pending</option>
                                    <option value="Approved" {{ (!empty(Request::get('q_applications')) && Request::get('q_applications') === "Approved") ? "selected" : ""}}>Approved</option>
                                    <!--<option value="Deleted" {{ (!empty(Request::get('q_applications')) && Request::get('q_applications') === "Deleted") ? "selected" : ""}}>Deleted</option>-->
                                </select>
                            </div>

                            @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                                    <select class="custom-select custom-select-sm" id="properties-field"  name="q_properties">

                                        <option value hidden>All Properties</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}"  {{ (!empty(Request::get('q_properties')) && Request::get('q_properties') == $property->id) ? "selected" : ""}}>{{ $property->full_address }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="input-group input-group-sm mr-0 mr-sm-3">
                                <input type="text" class="form-control" placeholder="Search by name" aria-label="Search by name" aria-describedby="button-addon2" id="search-field" name="q_search" value="{{ !empty(Request::get('q_search')) ? Request::get('q_search') : "" }}">
                                <div class="input-group-append">
                                    <a href="{{ route('applications') }}" class="btn btn-primary" type="button" id="button-addon2"><i class="fal fa-times"></i></a>
                                </div>
                            </div>

                            <a href="{{ route('applications/add') }}" class="btn btn-primary btn-sm mr-3"><i class="fal fa-plus-circle mr-1"></i> Create Application</a>
                            @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                <a href="#" data-toggle="modal" data-target="#inviteModal" class="btn btn-primary btn-sm"><i class="fal fa-pennant mr-1"></i> Invite Tenant</a>
                            @endif
                        </div>

                    </form>
                </div>

            </div>

            <div class="container-fluid">
                <div id="applicationsCardBox" class="applicationsCardBox mb-3">
                    @if ($applications->count())
                        @foreach($applications as $application)
                            <div class="card applicationCard">
                                <div class="card-body p-2">
                                    <div class="applicationCardImgSell text-center">
                                        <a href="{{ route('applications/view', ['id' => $application->id]) }}" class="applicationCardImg text-secondary" data-toggle="tooltip" data-placement="top" title="View Application"
                                           @if($tenantPhoto = $application->getTenantPhotoByEmail())
                                           style="background-image: url({{ $tenantPhoto }})"
                                           @endif
                                        >
                                            <i class="fal fa-user-check"></i>
                                        </a>
                                    </div>
                                    <div class="applicationCardBody fourNavIcons">
                                        <div class="ml-2 card-text">
                                            <span class="mr-2 text-secondary">{{$application->custom_created_at}}</span>
                                            {{ $application->firstname }} {{ $application->lastname }}
                                            {!!  $application->is_new()  ? "<span class='badge badge-danger'>New</span>" : "" !!}

                                            @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                                @if(Auth::user()->hasAddon('screening'))
                                                    @php
                                                        $screeningRequest = $application->addonScreening->first()
                                                    @endphp
                                                    @if(empty($screeningRequest))
                                                        <a href="{{ route('addon/screening', ['id' => $application->id]) }}" class="text-primary2 ml-3"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Request Tenant Screening</span></a>
                                                    @else
                                                        @php
                                                            $screeningRequest = $application->addonScreening->where('result', '!=', NULL)->first();
                                                        @endphp
                                                        @if(empty($screeningRequest->result))
                                                            <a href="{{ route('addon/screening', ['id' => $application->id]) }}" class="text-secondary ml-3" data-toggle="tooltip" data-placement="top" title="Resend Screening Request"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Tenant Screening Requested</span></a>
                                                        @else
                                                            <a href="{{ $screeningRequest->result }}" target="_blank" class="text-success ml-3" data-toggle="tooltip" data-placement="top" title="View Tenant Screening Report"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Tenant Screening Report</span></a>
                                                        @endif
                                                    @endif
                                                @else
                                                    <a href="{{ route('addon/screening/adv') }}" class="text-secondary ml-3"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Screen Tenant</span></a>
                                                @endif
                                            @endif
                                        </div>
                                        @if ($application->unit)
                                            <div class="ml-2"><span class="mr-2 text-secondary">Applied for</span> <span class="mr-2"> {{ $application->unit->property->full_address }}, </span> <span class="text-secondary d-none d-lg-inline"> {{$application->unit->name}}</span></div>
                                        @endif
                                    </div>
                                    <div class="applicationAmenities text-muted">
                                        <small><i class="fas fa-birthday-cake"></i><span> {{ $application->custom_d_o_b }}</span></small>
                                        <small>
                                            @if(empty($application->full_income))
                                                <i class="fal fa-dollar-sign"></i><span> No income listed </span>
                                            @else
                                                <span> Income: <i class="fal fa-dollar-sign"></i>{{ financeFormat($application->full_income) }} </span>
                                            @endif
                                        </small>
                                        <small><i class="fas fa-paw"></i><span> {{ $application->pets->count() ? "Has ". $application->pets->count() ." pets" : "No pets" }}</span></small>

                                        <small><i class="fas fa-smoking"></i><span> {{ $application->smoke ?  "Smoker" : "Non-smoker" }}</span></small>
                                    </div>
                                    <div class="applicationCardNav @if(Auth::user()->isLandlord() || Auth::user()->isPropManager()) fourNavIcons @endif">
                                        <span data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="{{ $application->id }}" data-record-title="{{ $application->firstname }} {{ $application->lastname }}'s Application #{{ $application->id }}" >
                                            <a href="#" data-toggle="tooltip" data-placement="top" title="Delete Application" class="btn btn-sm btn-light mr-1 text-danger"><i class="fal fa-trash-alt"></i><span class="d-lg-none"> Delete</span></a>
                                        </span>
                                        @if(Auth::user()->id == $application->user_id )
                                            <a href="{{ route('applications/view-edit', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="View/Edit Application" class="btn btn-sm btn-light mr-1 text-black"><i class="fal fa-wrench"></i><span class="d-lg-none"> View/Edit</span></a>
                                        @else
                                            <a href="{{ route('applications/view', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="View Application" class="btn btn-sm btn-light mr-1 text-black"><i class="fal fa-eye"></i><span class="d-lg-none"> View</span></a>
                                        @endif

                                        @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                            <a href="{{ route('leases/add', ['unit' => $application->unit_id, 'application' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="Create Lease" class="btn btn-sm btn-light mr-1 text-success"><i class="fal fa-file-signature"></i><span class="d-lg-none"> Create Lease</span></a>
                                        @endif

                                        <a href="{{ route('applications/share', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="Share Application" class="btn btn-sm btn-light mr-1"><i class="fal fa-share-alt" aria-hidden="true"></i><span class="d-lg-none"> Share Application</span></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($applications instanceof \Illuminate\Pagination\LengthAwarePaginator )
                            @php
                                $appends = [];
                                if (!empty(Request::get('q_search'))) $appends['q_search'] = Request::get('q_search');
                                if (!empty(Request::get('q_properties'))) $appends['q_properties'] = Request::get('q_properties');
                                if (!empty(Request::get('q_applications'))) $appends['q_applications'] = Request::get('q_applications');
                            @endphp


                            @if (!empty($appends))
                                {{ $applications->appends($appends)->onEachSide(1)->render() }}
                            @else
                                {{ $applications->onEachSide(1)->render('vendor.pagination.custom') }}
                            @endif
                        @endif

                    @else
                        <div class="propCardWrap">
                            <p class="alert alert-warning">
                                @if ($applicationsCountWithoutFilter)
                                    There are no applications that matching the filter.
                                @else
                                    You didn't create any applications yet. Press <a href="{{ url('applications/add') }}">"Add New Applications"</a> to create new application.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

            </div>
        @else
            <div class="p-3">
                <div class="text-center text-sm-left pt-1 pb-2">
                    <h1 class="h2">Applications</h1>
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

    </div>

    <!-- DELETE RECORD confirmation dialog-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>You are about to delete <b><i class="title"></i></b>, this procedure is irreversible.</p>
                    <div>Do you want to proceed?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger btn-ok"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-labelledby="inviteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('applications/invite-tenant/post') }}" id="invite_form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="inviteModalTitle">Invite your potential tenant to submit an application</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label for="share_yo_email_address">Tenant Email <i class="required fal fa-asterisk"></i></label>
                            <input name="email" id="share_yo_email_address" type="text" value="" class="form-control">
                            <span class="invalid-feedback" role="alert" id="validation-error-email" style="display: none;">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fal fa-pennant mr-1"></i> Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    var filterfrom = $('#application-filter-form')
    var filterInputs = filterfrom.find('input')
    var filterSelects = filterfrom.find('select')
    filterSelects.each(function (i, el) {
        el.onchange = function (event) {
            filterfrom.submit()
        }
    });
    filterInputs.each(function (i, el) {
        el.onchange = function (event) {
            filterfrom.submit()
        }
    });

    $( document ).ready(function() {
        // DELETE RECORD
        $('#confirmDeleteModal').on('click', '.btn-ok', function (e) {
            e.preventDefault();
            var id = $(this).data('record-id');
            $.ajax({
                url: '{{ route('applications/delete') }}',
                data: {
                    '_token': '{!! csrf_token() !!}',
                    'id': id
                },
                type: 'DELETE',
                success: function (response) {
                    location.href = '{!! route('applications') !!}  '
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });

        $('#confirmDeleteModal').on('show.bs.modal', function (event) {
            var t = $(event.relatedTarget);
            $(this).find('.title').text(t.data('record-title'));
            $(this).find('.btn-ok').data('record-id', t.data('record-id'));
        });

        $("#invite_form").submit(function (e) {
            var form = $(this);
            if(form.data('validated') === '1'){
                return;
            }
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: '{{ route('applications/invite-tenant-validate/post') }}',
                data: form.serialize(),
                success: function (data) {
                    form.data('validated', '1');
                    form.submit();
                },
                error: function (data) {
                    var $errors = data.responseJSON.errors
                    Object.keys($errors).forEach(i => {
                        var errBlock = $("#validation-error-" + i)
                        errBlock.children('strong').text($errors[i][0])
                    errBlock.css('display', 'block')
                })
                }
            });
        });
    });
</script>
@endsection
