@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('applications') }}">Applications</a> > <a href="#">View</a>
    </div>
    <div class="container-fluid pb-4">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Application</h1>
                    <div class="pb-3">
                    @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                        <h6 class="text-center text-sm-left mb-0">Applied Date/Time: {{ $application->applied_time() }} </h6>
                    @else
                        <h6 class="text-center text-sm-left mb-0">Created at: {{ \Carbon\Carbon::parse($application->created_at)->format('M d, Y. g:ia') }} </h6>
                        @foreach($application->shared_with() as $email => $time)
                            <h6 class="text-center text-sm-left mb-0">Shared with: {{ $email }} on {{ $time }} </h6>
                        @endforeach
                    @endif
                    </div>
                </div>
                <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                    @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                        @if(Auth::user()->hasAddon('screening'))
                            @php
                                $screeningRequest = $application->addonScreening->first()
                            @endphp
                            @if(empty($screeningRequest))
                                <a href="{{ route('addon/screening', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="Request Tenant Screening" class="btn btn-sm btn-primary mr-3"><i class="fal fa-shield" aria-hidden="true"></i><span> Tenant Screening</span></a>
                            @else
                                @php
                                    $screeningRequest = $application->addonScreening->where('result', '!=', NULL)->first();
                                @endphp
                                @if(empty($screeningRequest->result))
                                    <a href="{{ route('addon/screening', ['id' => $application->id]) }}" class="btn btn-sm btn-secondary mr-3" data-toggle="tooltip" data-placement="top" title="Tenant Screening Request Sent. Send Again"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Tenant Screening</span></a>
                                @else
                                    <a href="{{ $screeningRequest->result }}" target="_blank" class="btn btn-sm btn-success mr-3" data-toggle="tooltip" data-placement="top" title="View Tenant Screening Report"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Tenant Screening Report</span></a>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('addon/screening/adv') }}" class="btn btn-sm btn-primary mr-3"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Screen Tenant</span></a>
                        @endif
                        <a href="{{ route('applications/share', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="Share Application" class="btn btn-sm btn-primary mr-3"><i class="fal fa-share-alt" aria-hidden="true"></i><span> Share</span></a>
                        <span data-toggle="modal" data-target="#confirmDeleteModal">
                            <a href="#" class="btn btn-danger btn-sm mr-3"><i class="fal fa-trash-alt mr-1"></i> Delete</a>
                        </span>
                        <a href="{{ route('leases/add', ['unit' => $application->unit_id, 'application' => $application->id]) }}" class="btn btn-success btn-sm"><i class="fal fa-file-signature mr-1"></i> Create Lease</a>
                    @else
                        <a href="{{ route('applications/share', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="Share Application" class="btn btn-sm btn-primary mr-3"><i class="fal fa-share-alt" aria-hidden="true"></i><span> Share</span></a>
                        <span data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="21" data-record-title="Application #"{{$application->id}} >
                            <a href="#" class="btn btn-danger btn-sm mr-3"><i class="fal fa-trash-alt mr-1"></i> Delete</a>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="container-fluid">
            {{--@if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session()->get('success') }}
                </div>
            @endif--}}

            <div class="card propertyForm">


                <div class="row no-gutters innerCardRow">
                    <div class="col-md-7 bg-light">

                        <div class="card-header border-right d-block d-xl-none">
                            Tenant Information
                        </div>
                        <div class="tenantCard bgBigArrow card-body bg-light">
                            <div class="tenantCardRow">
                                <div class="tenantImgCell">
                                    <div class="tenantImg"
                                        @if($tenantPhoto = $application->getTenantPhotoByEmail())
                                            style="background-image: url({{ $tenantPhoto }})"
                                        @endif
                                    >
                                        <i class="fal fa-user-check"></i>
                                    </div>
                                </div>
                                <div class="tenantInfoCell">
                                    <div class="tenantGeneralInfo">
                                        <div class="h1">{{ $application->firstname }} {{ $application->lastname }}</div>
                                        <div class="h6">Phone: {{ $application->phone }}</div>
                                        @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                            <div class="h6">E-mail: <a class="text-primary2" href="mailto:{{ $application->email }}"><u>{{ $application->email }}</u></a></div>
                                        @else
                                            <div class="h6">E-mail: {{ $application->email }}</div>
                                        @endif
                                    </div>
                                    <div class="tenantAmenities">
                                        <span><i class="fas fa-birthday-cake"></i><span> {{ $application->custom_d_o_b }}</span></span>
                                        <span><i class="fas fa-dollar-sign"></i><span> {{ !empty($application->full_income) ? "Monthly Income: ". financeCurrencyFormat($application->full_income) : "No income listed" }}</span></span>
                                        <span><i class="fas fa-paw"></i><span> {{ $application->pets->count() ? "Has ". $application->pets->count() ." pets" : "No pets" }}</span></span>
                                        <span><i class="fas fa-smoking"></i><span> {{ $application->smoke ?  "Smoker" : "Non-smoker" }}</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 bg-light">

                        <div class="card-header d-block d-xl-none">
                            Unit Information
                        </div>
                        <div class="tenantCard tenantUnitCard card-body bg-light">
                            <div class="tenantCardRow">
                                <div class="tenantUnitImgCell">
                                    <div class="tenantUnitImg"
                                         @if ($unit = $application->unit)
                                             @if($unit->imageUrl())
                                                style="background-image: url({{ $unit->imageUrl() }});"
                                             @else
                                                 @if($unit->property->imageUrl())
                                                    style="background-image: url({{ $unit->property->imageUrl() }});"
                                                 @endif
                                            @endif
                                         @endif
                                    >
                                        @if ($unit = $application->unit)
                                            {!! $unit->property->icon() !!}
                                        @else
                                            <i class="fal fa-home-lg-alt"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="tenantUnitInfoCell">
                                    <div class="tenantUnitGeneralInfo">
                                        @if (!empty($application->unit))
                                            @if ($application->unit->isOccupied())
                                                <span class="badge badge-danger align-top">Occupied</span>
                                            @else
                                                <span class="badge badge-success align-top">Vacant</span>
                                            @endif
                                            <div class="h4">{{ $application->unit->name }}</div>
                                            <div class="h6 text-sm-left pb-3">{{ $application->unit->property->full_address }}</div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="row no-gutters innerCardRow">
                    <div class="col-md-6 border-right bg-light">
                        <div class="card-header border-top">
                            Employment & Monthly Income
                        </div>
                        <div class="card-body">
                            {{ $application->employmentAndlIncomes->count() ? "" : 'No employment history added' }}
                            @foreach($application->employmentAndlIncomes as $employmentAndincome)
                                {{ $employmentAndincome->employment }}: {{ financeCurrencyFormat($employmentAndincome->income) }}<br>
                            @endforeach

                        </div>
                    </div>
                    <div class="col-md-6 bg-light">
                        <div class="card-header border-top">
                            Additional Monthly Income
                        </div>
                        <div class="card-body">
                            {{ $application->incomes->count() ? "" : 'No income sources added' }}
                            @foreach($application->incomes as $income)
                                {{ $income->description }}: {{ financeCurrencyFormat($income->amount) }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row no-gutters innerCardRow">
                    <div class="col-md-6 border-right bg-light">
                        <div class="card-header border-top">
                            Residence history
                        </div>
                        <div class="card-body">
                            {{ $application->residenceHistories->count()  ? "" : "No residence history added"}}
                            @foreach($application->residenceHistories as $residenceHistory)
                                {{ $residenceHistory->full_address }}: {{ $residenceHistory->custom_start_date }} {{ "- ".  $residenceHistory->custom_end_date}}<br>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6 bg-light">
                        <div class="card-header border-top">
                            References
                        </div>
                        <div class="card-body">
                            {{ $application->references->count()  ? "" : "No References added"}}
                            @foreach($application->references as $reference)
                                {{ $reference->name }}
                                {{ $reference->email }} {{ $reference->phone}}<br>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row no-gutters innerCardRow">
                    <div class="col-12 bg-light">

                        <div class="card-header border-top">
                            Additional Info
                        </div>

                        <div class="card-body">
                            <p>Do you smoke?
                                <span class="badge {{ !!$application->smoke ? "badge-danger" : " badge-success"}}">
                                    {{ !empty($application->smoke) ? "YES" : "NO"}}
                                </span>
                            </p>

                            <p>Have you ever been evicted from a rental or had an unlawful detainer judgement against you?
                                <span class="badge {{ !!$application->evicted_or_unlawful ? "badge-danger" : " badge-success"}}">
                                    {{ !empty($application->evicted_or_unlawful) ? "YES" : "NO"}}
                                </span>
                            </p>

                            <p>Have you ever been convicted of a felony or misdemeanor (other than a traffic or parking violation)?
                                <span class="badge {{ !!$application->felony_or_misdemeanor ? "badge-danger" : " badge-success"}}">
                                    {{ !empty($application->felony_or_misdemeanor) ? "YES" : "NO"}}
                                </span>
                            </p>

                            <p>Have you ever refused to pay rent when it was due?
                                <span class="badge {{ !!$application->refuse_to_pay_rent ? "badge-danger" : " badge-success"}}">
                                    {{ !empty($application->refuse_to_pay_rent) ? "YES" : "NO"}}
                                </span>
                            </p>

                        </div>
                    </div>
                </div>

                @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                    <div class="row no-gutters innerCardRow">
                        <div class="col-md-6 border-right bg-light">
                            <div class="card-header border-top d-flex justify-content-between withButton">
                                Internal Notes
                                <button style="margin: -5px 0;" data-section="Edit Internal Notes" data-request="{{ route('applications/ajax-edit-internal-notes', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">{{ $application->internal_notes ? 'Edit' : 'Add' }} <i class="fas fa-pencil-alt ml-1"></i></button>
                            </div>
                            <div class="card-body">
                                {{ $application->internal_notes ?? "No notes added" }}
                            </div>
                        </div>
                        <div class="col-md-6 bg-light">
                            <div class="card-header border-top">
                                Notes from tenant
                            </div>
                            <div class="card-body">
                                {{ $application->notes ?? "No notes added" }}
                            </div>
                        </div>
                    </div>
                @else
                    @if(isset($application->notes))
                        <div class="row no-gutters innerCardRow">
                            <div class="col-12 bg-light">
                                <div class="card-header border-top">
                                    Notes
                                </div>
                                <div class="card-body">
                                    {{ $application->notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if(!empty($documents) && ($documents->count() > 0))
                    <div class="row no-gutters innerCardRow">
                        <div class="col-12 bg-light">
                            <div class="card-header border-top greyCardHead">
                                {{--}}<i class="fal fa-file-alt"></i> {{--}}Supportive Documents
                            </div>
                            <div class="card-body bg-light">
                                <ul id="sharedFileList" class="sharedFileList list-group">
                                    @foreach ($documents as $document)
                                        <li class="list-group-item list-group-item-action" data-documentid="{{ $document->id }}">
                                            <a class="sharedFileLink" href="/storage/{{ $document->filepath }}" target="_blank">{!! $document->icon() !!} <span>{{ $document->name }}</span></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
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
                    <p>You are about to delete the application, this procedure is irreversible.</p>
                    <div>Do you want to proceed?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-ok">Delete</button>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
        <!-- EDIT LIST -->
        <div class="modal fade" tabindex="-1" role="dialog" id="editListModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="needs-validation" novalidate method="post" action="{{ route('applications/edit-save', ['id' => $application->id]) }}" id="add-application">
                        @csrf
                        <div class="modal-body bg-light" id="modal-list-body">
                            <div class="loading">&nbsp;</div>
                            <div id="modal-list-body">
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                            <button type="submit" class="btn btn-primary" id="modal-list-save"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                        </div>
                        <input type="hidden" name="return" value="applications/view">
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        // DELETE RECORD
        $('#confirmDeleteModal').on('click', '.btn-ok', function(e) {
            e.preventDefault();
            var id = $(this).data('record-id');
            $.ajax({
                url: '{{ route('applications/delete') }}',
                data: {
                    '_token': '{!! csrf_token() !!}',
                    'id' : '{{ $application->id }}'
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
        $('#confirmDeleteModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('.title').text(t.data('record-title'));
            $(this).find('.btn-ok').data('record-id', t.data('record-id'));
        });
    </script>
    @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
        <script>
            $(document).ready(function() {
                $('#editListModal').on('show.bs.modal', function (event) {
                    var t = $(event.relatedTarget);
                    $('#editListModal').find('loading').show();
                    $(this).find('.modal-title').text(t.data('section'));
                    $('#modal-list-body').load(t.data('request'), function () {
                        $('#editListModal').find('loading').hide();
                        if($('#modal-list-body').find('.savedRow').length === 0){
                            $('#modal-list-body').find('.addRowButton').click();
                            //window.setTimeout("$('#modal-list-body').find('.addRowButton').click();", 500);
                        }
                    });
                });

            });
        </script>
    @endif
@endsection
