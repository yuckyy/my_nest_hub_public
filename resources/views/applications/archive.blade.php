@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('applications') }}">Applications</a> > <a href="#">Archive</a>
    </div>
    <div class="container-fluid">

        @if (true)

            <div class="filterApplicationsBox container-fluid">
                <div class="d-block d-sm-block d-lg-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">

                    <div class="text-center text-sm-left">
                        <h1 class="h2 d-inline-block">Archived Applications</h1>
                        <span class="badge badge-dark align-top">{{ $applications->total() }} total</span>
                    </div>

                    <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                        <a href="{{ route('applications') }}" class="btn btn-outline-secondary btn-sm mr-3"><i class="fal fa-times mr-1"></i> Exit Archive</a>
                    </div>

                </div>

            </div>

            <div class="container-fluid">
                <div id="applicationsCardBox" class="applicationsCardBox mb-3">
                    @if ($applications->count())
                        @foreach($applications as $application)
                            <div class="card applicationCard">
                                <div class="card-body p-2">
                                    <div class="applicationCardImgSell text-center">
                                        <span href="{{ route('applications/view', ['id' => $application->id]) }}" class="applicationCardImg text-secondary" data-toggle="tooltip" data-placement="top" title="View Application"
                                           @if($tenantPhoto = $application->getTenantPhotoByEmail())
                                           style="background-image: url({{ $tenantPhoto }})"
                                           @endif
                                        >
                                            <i class="fal fa-user-check"></i>
                                        </span>
                                    </div>
                                    <div class="applicationCardBody fourNavIcons">
                                        <div class="ml-2 card-text">
                                            <span class="mr-2 text-secondary">{{$application->custom_created_at}}</span>
                                            {{ $application->firstname }} {{ $application->lastname }}
                                            {!!  $application->is_new()  ? "<span class='badge badge-danger'>New</span>" : "" !!}
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
                                    <div class="applicationCardNav">
                                        <span class="float-right" data-toggle="modal" data-target="#confirmUnArchiveModal" data-record-id="{{ $application->id }}" data-record-title="{{ $application->firstname }} {{ $application->lastname }}'s Application #{{ $application->id }}" >
                                            <a href="#" data-toggle="tooltip" data-placement="top" title="Unarchive Application" class="btn btn-sm btn-light mr-1 text-muted"><i class="fal fa-box-open"></i><span class="d-lg-none"> Unarchive</span></a>
                                        </span>
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
        @endif

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
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary btn-ok"><i class="fal fa-box-open mr-1"></i> Unarchive</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $( document ).ready(function() {

        // UNARCHIVE RECORD
        $('#confirmUnArchiveModal').on('click', '.btn-ok', function(e) {
            var id = $(this).data('record-id');

            var form_data = new FormData();
            form_data.append("record_id", id);
            form_data.append("_token", '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('ajax_application_unarchive') }}',
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
@endsection
