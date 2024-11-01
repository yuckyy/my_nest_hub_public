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
                    <h6 class="text-center text-sm-left pb-3">Applied: {{ $application->custom_created_at }} </h6>
                </div>
                <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                    @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                        @if(Auth::user()->hasAddon('screening'))
                            @php
                                $screeningRequest = $application->addonScreening->first()
                            @endphp
                            @if(empty($screeningRequest))
                                <a href="{{ route('addon/screening', ['id' => $application->id]) }}" data-toggle="tooltip" data-placement="top" title="Request Tennant Screening" class="btn btn-sm btn-primary mr-3"><i class="fal fa-shield" aria-hidden="true"></i><span> Tennant Screening</span></a>
                            @else
                                @php
                                    $screeningRequest = $application->addonScreening->where('result', '!=', NULL)->first();
                                @endphp
                                @if(empty($screeningRequest->result))
                                    <a href="{{ route('addon/screening', ['id' => $application->id]) }}" class="btn btn-sm btn-secondary mr-3" data-toggle="tooltip" data-placement="top" title="Tennant Screening Request Sent. Send Again"><i class="fas fa-shield-alt" aria-hidden="true"></i><span> Tenant Screening</span></a>
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
                                    <div class="tenantGeneralInfo generalEditableBlock">

                                        <div class="editableBox mb-2" data-toggle="modal" data-target="#editNameModal">
                                            <span class="editThis h1">
                                                <span class="editableData"
                                                        id="firstnameLastnameView"
                                                        data-pk="firstnameLastname"
                                                        data-type="text"
                                                        data-label="Tenant Name"
                                                >{{ $application->firstname }} {{ $application->lastname }}</span><i class="fas fa-pencil-alt"></i>
                                            </span>
                                        </div>

                                        <div class="editableBox mb-1" data-toggle="modal" data-target="#editPhoneModal">
                                            <span class="editThis h6">
                                                Phone: <span class="editableData"
                                                      id="phoneView"
                                                      data-pk="phone"
                                                      data-type="text"
                                                      data-label="Tenant Name"
                                                >{{ $application->phone }}</span>
                                                <i class="fas fa-pencil-alt"></i>
                                            </span>
                                        </div>

                                        <div class="h6">Email: {{ $application->email }}</div>
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
                                    <div class="tenantUnitGeneralInfo generalEditableBlock">
                                        @if (!empty($application->unit))
                                            @if ($application->unit->isOccupied())
                                                <span class="badge badge-danger align-top">Occupied</span>
                                            @else
                                                <span class="badge badge-success align-top">Vacant</span>
                                            @endif
                                            @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
                                                <div class="editableBox mb-1" data-toggle="modal" data-target="#editUnitModal">
                                                    <div>
                                                        <span class="editThis h4">
                                                            <span class="editableData">{{ $application->unit->name }}</span><i class="fas fa-pencil-alt"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="editThis h6 text-sm-left pb-3">
                                                            <span class="editableData">{{ $application->unit->property->full_address }}</span><i class="fas fa-pencil-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="h4">{{ $application->unit->name }}</div>
                                                <div class="h6 text-sm-left pb-3">{{ $application->unit->property->full_address }}</div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="row no-gutters innerCardRow">
                    <div class="col-md-6 border-right bg-light">
                        <div class="card-header border-top d-flex justify-content-between withButton">
                            Employment & Monthly Income
                            <button style="margin: -5px 0;" data-section="Edit Employment & Monthly Income" data-request="{{ route('applications/ajax-edit-employment-and-incomes', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
                        </div>
                        <div class="card-body">
                            {{ $application->employmentAndlIncomes->count() ? "" : 'No employment history added' }}
                            @foreach($application->employmentAndlIncomes as $employmentAndincome)
                                {{ $employmentAndincome->employment }}: {{ financeCurrencyFormat($employmentAndincome->income) }}<br>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6 bg-light">
                        <div class="card-header border-top d-flex justify-content-between withButton">
                            Additional Monthly Income
                            <button style="margin: -5px 0;" data-section="Edit Additional Monthly Income" data-request="{{ route('applications/ajax-edit-additional-incomes', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
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
                        <div class="card-header border-top d-flex justify-content-between withButton">
                            Residence history
                            <button style="margin: -5px 0;" data-section="Edit Residence history" data-request="{{ route('applications/ajax-edit-residence-histories', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
                        </div>
                        <div class="card-body">
                            {{ $application->residenceHistories->count()  ? "" : "No residence history added"}}
                            @foreach($application->residenceHistories as $residenceHistory)
                                {{ $residenceHistory->full_address }}: {{ $residenceHistory->custom_start_date }} {{ "- ".  $residenceHistory->custom_end_date}}<br>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6 bg-light">
                        <div class="card-header border-top d-flex justify-content-between withButton">
                            References
                            <button style="margin: -5px 0;" data-section="Edit References" data-request="{{ route('applications/ajax-edit-references', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
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
                        <div class="card-header border-top d-flex justify-content-between withButton">
                            Pets
                            <button style="margin: -5px 0;" data-section="Edit Pets" data-request="{{ route('applications/ajax-edit-pets', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
                        </div>
                        <div class="card-body">
                            {{ $application->pets->count()  ? "" : "No Pets added"}}
                            @php
                                $numItems = count($application->pets);
                                $i = 0;
                            @endphp
                            @foreach($application->pets as $pet)
                                {{ $pet->type->alias == "another (please describe)" ? "" : ($pet->type->name . ": ") }} {{ $pet->description}}{!! ++$i === $numItems ? "" : ";&nbsp;&nbsp; " !!}
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row no-gutters innerCardRow">
                    <div class="col-12 bg-light">

                        <div class="card-header border-top d-flex justify-content-between withButton">
                            Additional Info
                            <button style="margin: -5px 0;" data-section="Edit Additional Info" data-request="{{ route('applications/ajax-edit-additional-info', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
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
                                Notes
                            </div>
                            <div class="card-body">
                                {{ $application->notes ?? "No notes added" }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row no-gutters innerCardRow">
                        <div class="col-12 bg-light">
                            <div class="card-header border-top d-flex justify-content-between withButton">
                                My Notes
                                <button style="margin: -5px 0;" data-section="Edit Internal Notes" data-request="{{ route('applications/ajax-edit-notes', ['id' => $application]) }}" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editListModal">{{ $application->notes ? 'Edit' : 'Add' }} <i class="fas fa-pencil-alt ml-1"></i></button>
                            </div>
                            <div class="card-body">
                                {{ $application->notes ?? "No notes added" }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row no-gutters innerCardRow">
                    <div class="col-12 bg-light">
                        <div class="card-header border-top greyCardHead">
                            {{--}}<i class="fal fa-file-alt"></i> {{--}}Supportive Documents
                        </div>
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col">
                                    <div class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                        <div class="filesBox">
                                            <div class="h1 pb-1">
                                                <i class="fal fa-file-alt"></i>
                                            </div>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="documentUpload" multiple>
                                            <div class="custom-file-label btn btn-sm btn-primary" for="documentUpload" data-browse=""><i class="fal fa-upload"></i> Upload Documents</div>
                                        </div>
                                        <small>
                                            Upload here the attachments that you want to be visible to your landlord/property manager.<br>
                                            Maximum size: 5Mb.<br>
                                            Allowed file types: doc, pdf, txt, jpg, png, gif, xls, csv.
                                        </small>
                                    </div>
                                </div>
                                <div class="col @if(empty($documents) || ($documents->count() == 0)) d-none @endif">
                                    <ul id="sharedFileList" class="sharedFileList list-group">
                                        @if(!empty($documents))
                                            @foreach ($documents as $document)
                                                <li class="list-group-item list-group-item-action" data-documentid="{{ $document->id }}">
                                                    <a class="sharedFileLink" href="/storage/{{ $document->filepath }}" target="_blank">{!! $document->icon() !!} <span>{{ $document->name }}</span></a> <button class="btn btn-sm btn-cancel deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
                                                    <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- DELETE RECORD confirmation dialog -->
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
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger btn-ok">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT UNIT -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editUnitModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Property / Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="needs-validation" novalidate method="post" action="{{ route('applications/edit-save', ['id' => $application->id]) }}" id="add-application">
                    @csrf
                    <div class="modal-body bg-light">
                        <label for="validationCustom04">Property <i class="required fal fa-asterisk"></i></label>
                        <select name="property_id" class="custom-select property-select" id="validationCustom04" required="required">
                            <option hidden value>Choose a property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ !empty($currProperty) && ($currProperty->id == $property->id)  ? 'selected="selected"' : ""}}>
                                    {{ !empty($property->type) ? $property->type->name : "" }} - {{ $property->full_address }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" role="alert"></span>

                        <div class="pt-3">
                            <label for="validationCustom05">Unit <i class="required fal fa-asterisk"></i></label>
                            <select name="unit_id" class="custom-select inut-select" id="validationCustom05" required="required" >
                                <option hidden value="">Choose a unit</option>
                                @if (!empty($currProperty))
                                    @foreach($currProperty->units as $unit)
                                        <option value="{{ $unit->id }}" {{ $currUnit->id == $unit->id  ? "selected" : ""}}>{{ $unit->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT NAME -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editNameModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tenant Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="needs-validation" novalidate method="post" action="{{ route('applications/edit-save', ['id' => $application->id]) }}" id="add-application">
                    @csrf
                    <div class="modal-body bg-light">
                        <label for="firstnameField">
                            First Name <i class="required fal fa-asterisk"></i>
                        </label>
                        <input type="text" name="firstname" id="firstnameField" class="form-control" required="required" maxlength="255"
                               value="{{ $application->firstname }}">
                        <span class="invalid-feedback" role="alert">
                        </span>
                        <div class="pt-3">
                            <label for="lastnameField">
                                Last Name <i class="required fal fa-asterisk"></i>
                            </label>
                            <input type="text" name="lastname" id="lastnameField" class="form-control" required="required" maxlength="255"
                                   value="{{ $application->lastname }}">
                            <span class="invalid-feedback" role="alert">
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT PHONE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editPhoneModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tenant Phone</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="needs-validation" novalidate method="post" action="{{ route('applications/edit-save', ['id' => $application->id]) }}" id="add-application">
                    @csrf
                    <div class="modal-body bg-light">
                        <label for="phoneField">
                            Phone <i class="required fal fa-asterisk"></i>
                        </label>
                        <input type="text" name="phone" id="phoneField" class="form-control" required="required" data-mask="000-000-0000" maxlength="25"
                               value="{{ $application->phone }}">
                        <span class="invalid-feedback" role="alert">
                        </span>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="modal-list-save"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE FILE confirmation dialog-->
    <div class="modal fade" id="confirmFileDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmFileDeleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmFileDeleteModalTitle">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div>Are you sure you would like to delete <strong id="modalFileName"></strong>?</div>
                    <div class="loadingHolder"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                    <button id="confirmDeleteDocument" class="btn btn-danger btn-sm mr-3" type="button">
                        <i class="fal fa-trash mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function() {
            // DELETE RECORD
            $('#confirmDeleteModal').on('click', '.btn-ok', function (e) {
                e.preventDefault();
                var id = $(this).data('record-id');
                $.ajax({
                    url: '{{ route('applications/delete') }}',
                    data: {
                        '_token': '{!! csrf_token() !!}',
                        'id': '{{ $application->id }}'
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
            $('#editNameModal').on('show.bs.modal', function (event) {
                $('#firstnameField').val('{{ $application->firstname }}');
                $('#lastnameField').val('{{ $application->lastname }}');
            });
            $('#editPhoneModal').on('show.bs.modal', function (event) {
                $('#phoneField').val('{{ $application->phone }}');
            });
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

            function updateFormat(obj){
                if(jQuery(obj).attr("data-type") === 'currency') {
                    jQuery(obj).on({
                        keyup: function() {
                            formatCurrency(jQuery(this));
                        },
                        blur: function() {
                            formatCurrency(jQuery(this), "blur");
                        }
                    });
                }
                if(jQuery(obj).attr("data-type") === 'integer') {
                    jQuery(obj).on({
                        keyup: function() {
                            formatInteger(jQuery(this));
                        }
                    });
                }
                if(jQuery(obj).attr("data-maxamount")) {
                    jQuery(obj).on({
                        keyup: function() {
                            restrictMaxAmount(jQuery(this));
                        }
                    });
                }
                if(jQuery(obj).attr("data-type") === 'phone') {
                    jQuery(obj).mask('000-000-0000');
                }
            }

            //---
            $(document).on( "click", ".addRowButton", function( e ) {
                e.preventDefault();
                var n = $(this).data('n');
                n++;
                $(this).data('n', n);
                var target = $(this).data('target');
                var targrtbox = $('#' + target);
                var row = targrtbox.find('.rowTemplate').clone();
                row.removeClass('rowTemplate');
                var input = row.find('input');
                input.each(function () {
                    var toReplace = $(this).attr('id');
                    var replid = !!toReplace ? toReplace.replace("0", n) : false;
                    $(this).attr('id', replid);
                    var name = $(this).attr('name').replace("0", n);
                    //var errorBlock = $(this).next('span');
                    //var errorMsg = errorBlock.attr('name').replace("0", n);
                    //errorBlock.attr('name', errorMsg);
                    $(this).attr('name', name);
                    $(this).attr('disabled', false);
                    updateFormat(this);
                    if ($(this).hasClass('endDateCurrent')) {
                        $(this).change(function () {
                            endDateCheckboxInit(this);
                        });
                    }
                });
                row.find('select').each(function () {
                    var name = $(this).attr('name').replace("0", n);
                    $(this).attr('name', name);
                    $(this).attr('disabled', false);
                    updateFormat(this);
                    if ($(this).hasClass('endDateCurrent')) {
                        $(this).change(function () {
                            endDateCheckboxInit(this);
                        });
                    }
                });
                row.find('label').each(function () {
                    if ($(this).attr('for')) {
                        var replfor = $(this).attr('for').replace("0", n);
                        $(this).attr('for', replfor);
                    }
                });
                row.find('.removeFormRowCell').find('a').click(function (e) {
                    e.preventDefault();
                    $(this).parent().parent('.form-row').remove();
                });
                targrtbox.append(row);
            });

            $(document).on( "click", ".removeFormRowCell > a", function( e ) {
                e.preventDefault();
                $(this).parent().parent('.form-row').remove();
            });

            $('[name="property_id"]').on({
                change: function(event) {
                    var propertyId = $(this).find(":selected").val();
                    $.ajax({
                        url: '/api/property/' + propertyId + '/units',
                        type: 'GET',
                        success: function (data) {
                            var unitsBlock = $('[name="unit_id"]');
                            unitsBlock.empty();
                            unitsBlock.append('<option hidden value>Choose a unit</option>');
                            data.forEach(function callback(el, index, array) {
                                var opt = '<option value="' + el.id + '">'  + el.name + '</option>';
                                unitsBlock.append(opt);
                            });
                        },
                        error: function (data) {
                            console.log('error')
                        }
                    });
                }
            });


            $('#documentUpload').on('change', function () {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("application_id", '{{ $application->id }}');
                var ins = document.getElementById('documentUpload').files.length;
                var sizes_ok = true;
                var num_uploaded = 0;
                for (var x = 0; x < ins; x++) {
                    if(document.getElementById('documentUpload').files[x].size > 5000000){
                        sizes_ok = false;
                    } else {
                        form_data.append("documents[]", document.getElementById('documentUpload').files[x]);
                        num_uploaded++;
                    }
                }
                if((sizes_ok === false) && (num_uploaded === 0)){
                    alert("File is too big");
                    return;
                }

                var loadingbox =
                    '<li class="loadingBox list-group-item text-center list-group-item-action list-group-item-info bg-white">' +
                    '<img src="/images/loading.gif" style="margin:auto" />' +
                    '</li>';
                $('#sharedFileList').append(loadingbox);
                $('#sharedFileList').parent().removeClass('d-none');

                $.ajax({
                    url: '{{ route('applications/document-upload') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $('.loadingBox').remove();
                        var index;
                        var docbox = '';
                        for (index = 0; index < response.uploaded.length; ++index) {
                            if(response.uploaded[index].error){
                                docbox = docbox +
                                    '<li class="list-group-item list-group-item-action fileWithError list-group-item-danger">' +
                                    '<a class="sharedFileLink text-danger" href="javascript:void(0)">' + response.uploaded[index].icon +
                                    '<span>' + response.uploaded[index].name + '</span></a>' +
                                    '<strong class="float-right text-danger">' + response.uploaded[index].error + '</strong>' +
                                    '</li>';
                            } else {
                                docbox = docbox +
                                    '<li class="list-group-item list-group-item-action" data-documentid="' + response.uploaded[index].id + '">' +
                                    '<a class="sharedFileLink" href="' + response.uploaded[index].url + '" target="_blank">' + response.uploaded[index].icon +
                                    '<span>' + response.uploaded[index].name + '</span></a>' +
                                    '<button class="btn btn-sm btn-cancel deleteDocument" data-documentid="' + response.uploaded[index].id + '"><i class="fal fa-trash-alt mr-1"></i> Delete</button>' +
                                    '<input type="hidden" name="document_ids[]" value="' + response.uploaded[index].id + '">' +
                                    '</li>';
                            }
                        }
                        $('#sharedFileList').append(docbox);
                        window.setTimeout('$(".fileWithError").fadeOut("fast")', 3000);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            $(document).on('click', '.deleteDocument', function(event){
                event.stopPropagation();
                event.preventDefault();
                var documentid = $(this).data('documentid');
                var document_name = $(this).parent("li").find("span").text();
                $("#confirmDeleteDocument").data('documentid', documentid);
                $("#modalFileName").text(document_name);
                $('#confirmFileDeleteModal').modal();
            });

            $(document).on('click', '#confirmDeleteDocument', function(event){
                event.preventDefault();
                var documentid = $(this).data('documentid');
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("document_id", documentid);
                $(".preloader").fadeIn("slow");
                $.ajax({
                    url: '{{ route('applications/document-delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $(".preloader").fadeOut("fast");
                        $('.sharedFileList').find('li[data-documentid=' + response.document_id + ']').remove();
                        $('.sharedFileList').find('a[data-documentid=' + response.document_id + ']').remove();
                        $('#confirmFileDeleteModal').modal('hide');
                    },
                    error: function (response) {
                        $('#confirmFileDeleteModal').modal('hide');
                        console.log(response);
                    }
                });
            });

        });
    </script>

@endsection
