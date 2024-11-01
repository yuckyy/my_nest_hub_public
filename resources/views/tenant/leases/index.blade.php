@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Lease</a>
    </div>

    @if (count($activeLeases) > 0 || count($inactiveLeases) > 0)
        <div class="container-fluid pb-4">

            <div class="container-fluid">
                <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2">
                    <div>
                        <h1 class="h2">Lease</h1>
                    </div>

                    <div class="leaseFilterToolbar btn-toolbar mb-2">
                        <div class="input-group input-group-sm">

                            <select class="custom-select custom-select-sm leases-list">
                                <option value="">Current/Previous lease(s)</option>
                                @if (count($activeLeases) > 0)
                                    <optgroup label="Current lease(s)">
                                        @foreach ($activeLeases as $lease)
                                            <option value="{{ $lease->id }}" @if($selectedLease && ($selectedLease->id == $lease->id)) selected="selected" @endif>
                                                Lease:&nbsp;
                                                {{ $lease->custom_start_date }}&nbsp;
                                                -&nbsp;
                                                {{ $lease->custom_end_date }}&nbsp;
                                                Landlord: {{ $lease->unit->property->user->name }} {{ $lease->unit->property->user->lastname }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if (count($inactiveLeases) > 0)
                                <optgroup label="Previous lease(s)">
                                    @foreach ($inactiveLeases as $lease)
                                        <option value="{{ $lease->id }}" @if($selectedLease && ($selectedLease->id == $lease->id)) selected="selected" @endif>
                                            Lease:&nbsp;
                                            {{ $lease->custom_start_date }}&nbsp;
                                            -&nbsp;
                                            {{ $lease->custom_end_date }}&nbsp;
                                            Landlord: {{ $lease->unit->property->user->name }} {{ $lease->unit->property->user->lastname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endif
                            </select>

                        </div>

                    </div>
                </div>
            </div>

            <div class="container-fluid unitFormContainer">
                <div class="row">
                    {{-- <div class="navTabsLeftContainer col-md-3">
                    </div> --}}

                    <div class="col-12 inactiveLeaseContent">

                        @if ($selectedLease)

                            @if(!empty($selectedLease->deleted_at))
                                <p class="alert alert-danger">
                                    This lease is closed
                                </p>
                            @endif

                            <div class="card propertyForm mb-4">
                                <div class="card-body bg-light">
                                    <div class="row">
                                        <div class="col-md-6 editableBlock">
                                            <label>Property</label>
                                            <div class="editableBox">
                                                <span class="editThis">
                                                    <span class="editableData">{!! $selectedLease->unit->property->address !!}, {!! $selectedLease->unit->property->city !!}, {!! $selectedLease->unit->property->state->code !!}, {!! $selectedLease->unit->property->zip !!}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 editableBlock">
                                            <label>Unit</label>
                                            <div class="editableBox">
                                                <span class="editThis">
                                                    <span class="editableData">{!! $selectedLease->unit->name !!}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row no-gutters">
                                    <div class="col-md-6 bg-light border-right">

                                        <div class="card-header border-top">
                                            Tenant Information
                                        </div>
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>First Name</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="editableData">{!! $selectedLease->firstname !!}</span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Last Name</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="editableData">{!! $selectedLease->lastname !!}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6 editableBlock">
                                                    <label>Email Address</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="notEditableData">{!! $selectedLease->email !!}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 editableBlock">
                                                    <label>Phone</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="notEditableData">{!! $selectedLease->phone !!}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 bg-light">

                                        <div class="card-header border-top">
                                            Landlord Information
                                        </div>
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>First Name</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="editableData">{!! $selectedLease->unit->property->user->name !!}</span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Last Name</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="editableData">{!! $selectedLease->unit->property->user->lastname !!}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Email Address</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="editableData">{!! $selectedLease->unit->property->user->email !!}</span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3 editableBlock">
                                                    @if(!empty($selectedLease->unit->property->user->phone))
                                                    <label>Phone</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="editableData">{!! $selectedLease->unit->property->user->phone !!}</span>
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row no-gutters innerCardRow">

                                    <div class="col-lg-5 border-right bg-light border-right">
                                        <div class="card-header border-top">
                                            Lease Details
                                        </div>
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col mb-3 editableBlock">
                                                    <label>Start Date</label>
                                                    <div class="editableBox" data-type="date">
                                                        <span class="editThis">
                                                            <span
                                                                    class="editableData"
                                                                    id="start_date"
                                                                    data-pk="start_date"
                                                                    data-type="combodate"
                                                                    data-label="Start Date"
                                                            >{!! \Carbon\Carbon::parse($selectedLease->start_date)->format("m/d/Y") !!}</span>
                                                            
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-100 d-block d-md-none"></div>
                                                <div class="col mb-3 editableBlock">
                                                    <label>End Date</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                    class="editableData"
                                                                    id="end_date"
                                                                    data-pk="end_date"
                                                                    data-type="combodate"
                                                                    data-label="End Date"
                                                            >{!! $selectedLease->end_date ? \Carbon\Carbon::parse($selectedLease->end_date)->format("m/d/Y") : 'Month to Month' !!}</span>
                                                            
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col editableBlock">
                                                    <label>Monthly Due Date</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="naData">day</span>
                                                            <span
                                                                    class="editableData"
                                                                    id="monthly_due_date"
                                                                    data-pk="due_date"
                                                                    data-type="number"
                                                                    data-placement="top"
                                                                    data-label="Monthly Due Date"
                                                                    data-max="31"
                                                            >{!! $selectedLease->monthly_due_date !!}</span>
                                                            
                                                            <span class="naData">of the month</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 border-right bg-light border-right">
                                        <div class="card-header border-top">
                                            Automatic Late Fees
                                        </div>
                                        <div class="card-body">
                                                <div class="mb-3 editableBlock">
                                                    <label>Add Fee</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                    class="editableData"
                                                                    id="late_fee_day"
                                                                    data-pk="after_dueue_date"
                                                                    data-type="number"
                                                                    data-placement="top"
                                                                    data-label="Add Fee"
                                                                    data-max="31"
                                                            >{!! $selectedLease->late_fee_day !!}</span>

                                                            <span class="naData">day after rent is due</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="editableBlock">
                                                    <label>Late Fee Amount</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="naData">$</span>
                                                            <span
                                                                    class="editableData"
                                                                    id="late_fee_amount"
                                                                    data-pk="after_dueue_amount"
                                                                    data-type="numeric"
                                                                    data-label="Late Fee Amount"
                                                            >{!! financeFormat($selectedLease->late_fee_amount) !!}</span>

                                                        </span>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 border-top d-flex align-items-center">
                                        <div class="card-body">

                                            <div class="editableBlock rentAmount">
                                                <label class="justify-content-center">Monthly rent amount</label>
                                                <div class="editableBox text-center">
                                                    <span class="editThis">
                                                        <span class="naData">$</span>
                                                        <span
                                                                class="editableData"
                                                                id="amount"
                                                                data-pk="rent_amount"
                                                                data-type="numeric"
                                                                data-label="Monthly rent amount"
                                                        >{!! financeFormat($selectedLease->amount) !!}</span>
                                                        
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card propertyForm mb-4">
                                <div class="card-header">
                                    Collect Rent Assistance
                                </div>
                                <div class="card-body bg-light">
                                    <div class="form-row">
                                        <div class="col-md-4 editableBlock">
                                            <label>Section 8</label>
                                            <div class="editableBox">
                                                <span class="editThis">
                                                    <span class="naData">$</span>
                                                    <span
                                                            class="editableData"
                                                            id="section8"
                                                            data-type="numeric"
                                                            data-placement="top"
                                                            data-label="Section 8"
                                                    >{!! financeFormat($selectedLease->section8) !!}</span>
                                                    
                                                    <span class="naData">per month</span>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-4 editableBlock">
                                            <label>Military Discount</label>
                                            <div class="editableBox">
                                                <span class="editThis">
                                                    <span class="naData">$</span>
                                                    <span
                                                            class="editableData"
                                                            id="military"
                                                            data-type="numeric"
                                                            data-label="Military Discount"
                                                    >{!! financeFormat($selectedLease->military) !!}</span>
                                                    
                                                    <span class="naData">per month</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 editableBlock">
                                            <label>Other</label>
                                            <div class="editableBox">
                                            <span class="editThis">
                                                <span class="naData">$</span>
                                                <span
                                                        class="editableData"
                                                        id="other"
                                                        data-type="numeric"
                                                        data-label="Other"
                                                >{!! financeFormat($selectedLease->other) !!}</span>
                                                
                                                <span class="naData">per month</span>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-header border-top">
                                    Collect Bills
                                </div>
                                <div class="card-body bg-light">
                                    <div class="form-row bills-container">
                                        @foreach ($selectedLease->bills as $bill)
                                            <div class="col-md-4 mb-3 editableBlock">
                                                <label>{!! $bill->name !!}</label>
                                                <div class="editableBox">
                                                    <span class="editThis">
                                                        <span class="naData">$</span>
                                                        <span class="editableData" id="bill-{!! $bill->id !!}" data-label="{!! $bill->name !!} Bill" data-type="numeric" data-placement="top">{!! financeFormat($bill->value) !!}</span>
                                                        
                                                        <span class="naData">per month</span>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row no-gutters innerCardRow">
                                    <div class="col-md-6 bg-light border-right">
                                        <div class="card-header border-top">
                                            Collect Prorated Rent
                                        </div>
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Pro-Rated Rent Due</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                    class="editableData"
                                                                    id="prorated_rent_due"
                                                                    data-pk="prorated_due"
                                                                    data-type="combodate"
                                                                    data-label="Pro-Rated Rent Due"
                                                            >{!! !empty($selectedLease->prorated_rent_due) ? \Carbon\Carbon::parse($selectedLease->prorated_rent_due)->format("m/d/Y") : "Not specified" !!}</span>
                                                            
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Pro-Rated Rent Amount</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="naData">$</span>
                                                            <span
                                                                    class="editableData"
                                                                    id="prorated_rent_amount"
                                                                    data-pk="prorated_rent_amount"
                                                                    data-type="numeric"
                                                                    data-label="Pro-Rated Rent Amount"
                                                            >{!! financeFormat($selectedLease->prorated_rent_amount) !!}</span>
                                                            
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 bg-light border-top">
                                        <div class="card-header">
                                            Security Deposit
                                        </div>
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Security Deposit Amount</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span class="naData">$</span>
                                                            <span
                                                                    class="editableData"
                                                                    id="security_amount"
                                                                    data-pk="security_deposit_amount"
                                                                    data-type="numeric"
                                                                    data-label="Security Deposit Amount"
                                                            >{!! financeFormat($selectedLease->security_amount) !!}</span>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Due On</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                    class="editableData"
                                                                    id="security_deposit"
                                                                    data-pk="security_deposit"
                                                                    data-type="combodate"
                                                                    data-label="Due On"
                                                            >{!! !empty($selectedLease->security_deposit) ? \Carbon\Carbon::parse($selectedLease->security_deposit)->format("m/d/Y") : "Not specified" !!}</span>

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-header border-top">
                                    Move-in Costs
                                </div>
                                <div class="card-body bg-light">
                                    <div class="move-ins-container">
                                        @foreach ($selectedLease->moveIns as $moveIn)
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3 editableBlock">
                                                    <label>Amount</label>
                                                    <div class="editableBox">
                                                    <span class="editThis">
                                                        <span class="naData">$</span>
                                                        <span class="editableData" id="movein_amount-{!! $moveIn->id !!}" data-type="numeric" data-label="Move In Amount">{!! financeFormat($moveIn->amount) !!}</span>

                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3 editableBlock">
                                                    <label>Due On</label>
                                                    <div class="editableBox">
                                                    <span class="editThis">
                                                        <span class="editableData" id="movein_date-{!! $moveIn->id !!}" data-type="combodate" data-label="Move In Due On">{!! \Carbon\Carbon::parse($moveIn->due_on)->format("m/d/Y") !!}</span>

                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 mb-3 editableBlock">
                                                    <label>Memo</label>
                                                    <div class="editableBox">
                                                    <span class="editThis">
                                                        <span class="editableData" id="movein_memo-{!! $moveIn->id !!}" data-type="text" data-label="Move In Memo">{!! $moveIn->memo !!}</span>

                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="card propertyForm mb-4">
                                @if($selectedLease->sharedDocuments()->count() > 0)


                                    <div class="card-header">
                                        Shared documents
                                    </div>
                                    <div class="card-body bg-light">
                                        <div class="row">
                                            <div class="col">
                                                <ul id="sharedFileList" class="sharedFileList list-group">
                                                    @foreach ($selectedLease->sharedDocuments() as $document)
                                                    <li class="list-group-item list-group-item-action" data-documentid="{{ $document->id }}">
                                                        <a class="sharedFileLink" href="/storage/{{ $document->filepath }}" target="_blank">{!! $document->icon() !!} <span>{{ $document->name }}</span></a>
                                                        {{--@if (($selectedLease !== false) && empty($selectedLease->deleted_at))
                                                            <button class="btn btn-sm btn-cancel deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-times mr-1"></i> Delete</button>
                                                            <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                                        @endif--}}
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row no-gutters innerCardRow border-top">
                                    <div class="col-md-6 bg-light border-right">

                                        <div class="card-header">
                                            Move-in Photos
                                        </div>
                                        <div class="card-body bg-light">
                                            <div class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                                <div class="filesBox">
                                                    <div class="h1 pb-1">
                                                        <i class="fal fa-image"></i>
                                                    </div>
                                                </div>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input moveImageUpload" data-target_list="#photoList" data-document_type="move_in_photo" id="moveInImageUpload" multiple>
                                                    <div class="custom-file-label btn btn-sm btn-primary" for="moveInImageUpload" data-browse=""><i class="fal fa-upload"></i> Upload Move-in Photos</div>
                                                </div>
                                                <small>
                                                    Upload up to 15 photos. These files are not visible to your tenants.<br>
                                                    Maximum size: 5Mb.<br>
                                                    Allowed file types: jpg, png, gif.
                                                </small>
                                            </div>
                                            <div class="pt-3 @if($selectedLease->moveInPhotos()->count() == 0) d-none @endif ">
                                                    <div class="photoListBox sharedFileList" id="photoList">
                                                        @foreach ($selectedLease->moveInPhotos() as $document)
                                                            <a href="/storage/{{ $document->filepath }}" data-toggle="modal" data-target="#imageModal" class="galleryItem" data-photo_type="Move-in photo." data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}" data-documentid="{{ $document->id }}">
                                                                <div class="galleryItemContent" style="background-image: url(/storage/{{ $document->thumbnailpath ?? $document->filepath }})">
                                                                    <div class="gallaryControl galleryTrash deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt text-white"></i></div>
                                                                </div>
                                                            </a>
                                                            <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6 bg-light">

                                            <div class="card-header">
                                                Move-out Photos
                                            </div>
                                            <div class="card-body bg-light">
                                                <div class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                                    <div class="filesBox">
                                                        <div class="h1 pb-1">
                                                            <i class="fal fa-image"></i>
                                                        </div>
                                                    </div>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input moveImageUpload" data-target_list="#photoListMoveOut" data-document_type="move_out_photo" id="moveOutImageUpload" multiple>
                                                        <div class="custom-file-label btn btn-sm btn-primary" for="moveOutImageUpload" data-browse=""><i class="fal fa-upload"></i> Upload Move-out Photos</div>
                                                    </div>
                                                    <small>
                                                        Upload up to 15 photos. These files are not visible to your tenants.<br>
                                                        Maximum size: 5Mb.<br>
                                                        Allowed file types: jpg, png, gif.
                                                    </small>
                                                </div>
                                                <div class="pt-3 @if($selectedLease->moveOutPhotos()->count() == 0) d-none @endif ">
                                                    <div class="photoListBox sharedFileList" id="photoListMoveOut">
                                                        @foreach ($selectedLease->moveOutPhotos() as $document)
                                                            <a href="/storage/{{ $document->filepath }}" data-toggle="modal" data-target="#imageModal" class="galleryItem" data-photo_type="Move-out photo." data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}" data-documentid="{{ $document->id }}">
                                                                <div class="galleryItemContent" style="background-image: url(/storage/{{ $document->thumbnailpath ?? $document->filepath }})">
                                                                    <div class="gallaryControl galleryTrash deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt text-white"></i></div>
                                                                </div>
                                                            </a>
                                                            <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                            </div>


                                {{-- <div class="card propertyForm mb-4">
                                    <div class="card-header">
                                        Sending payments
                                    </div>
                                    <div class="card-body bg-light pb-0">
                                        <div class="inRowComment"><i class="fal fa-info-circle"></i> Link a checking account or debit card to receive your rent and one-time payments.</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="editableBlock">
                                                    <label>Finance Account</label>
                                                    <div class="editableBox">
                                                    <span class="editThis">
                                                        <span class="editableData" id="finance_account" data-pk="finance_account" data-type="select" data-placement="top" data-source="[{value: 1, text: 'Some Finance Account Name'},{value: 2, text: 'My Credit Card'}]" data-value="2">My Credit Card</span>

                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pt-3 text-right">
                                                <div class="addBillBox mb-3">
                                                    <a id="addFinanceAccountButton" data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="false" aria-controls="addFinanceAccountContent" class="btn btn-outline-secondary btn-sm collapsed"><i class="fal fa-plus-circle mr-1"></i><i class="fal fa-minus-circle mr-1"></i>Add Finance Account</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="collapse multi-collapse" id="addFinanceAccountContent">
                                        @component('add_finance_account_form')
                                        @endcomponent

                                        <div class="card-footer text-muted">
                                            <a  id="cancelCreateFinanceAccount" href="#" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                                            <button class="btn btn-primary btn-sm float-right"><i class="fal fa-check-circle mr-1"></i> Save Finance Account</button>
                                        </div>
                                    </div>
                                </div> --}}
                            {{--
                            @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
                                <div class="card propertyForm mb-4">
                                    <div class="card-header">
                                        Shared documents
                                    </div>
                                    <div class="card-body bg-light">
                                        <div class="row">
                                            <div class="col-md-8 pr-md-0">
                                                <ul class="sharedFileList list-group">
                                                    <li class="list-group-item list-group-item-action">
                                                        <a class="sharedFileLink" href="#"><i class="fal fa-file-pdf"></i> <span>SomeName--filename.pdf</span></a> <button class="btn btn-sm btn-cancel"><i class="fal fa-times mr-1"></i> Delete</button>
                                                    </li>
                                                    <li class="list-group-item list-group-item-action">
                                                        <a class="sharedFileLink" href="#"><i class="fal fa-file-alt"></i> <span>Dapibus.ppt</span></a> <button class="btn btn-sm btn-cancel"><i class="fal fa-times mr-1"></i> Delete</button>
                                                    </li>
                                                    <li class="list-group-item list-group-item-action">
                                                        <a class="sharedFileLink" href="#"><i class="fal fa-file-word"></i> <span>Document-11123444.doc</span></a> <button class="btn btn-sm btn-cancel"><i class="fal fa-times mr-1"></i> Delete</button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="leaseFormFileUploadBox card-body text-center bg-white border pb-1">
                                                    <div class="filesBox">
                                                        <div class="display-4 pb-3">
                                                            <i class="fal fa-file-alt"></i>
                                                        </div>
                                                    </div>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="customFileLangHTML" multiple>
                                                        <div class="custom-file-label btn btn-sm btn-primary" for="customFileLangHTML" data-browse=""><i class="fal fa-upload"></i> Upload Documents</div>
                                                    </div>
                                                    <small>Upload here the attachments that you want to be visible to your tenants.<br>Maximum size: 2Mb</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer text-muted">
                                        <h3>End Lease</h3>
                                        <div class="inRowComment">
                                            <i class="fal fa-info-circle"></i> We will immediately notify your tenants and stop all of their upcoming payments. Any payments that have already started will continue to process.
                                            You will still be able to access your previous transactions and tenant history.
                                        </div>

                                        <button class="btn btn-danger btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmEndLeaseModal">
                                            <i class="fal fa-times mr-1"></i> End Lease
                                        </button>
                                    </div>

                                        </div>
                                    @endif
                                    --}}
                        @else

                            <p class="alert alert-warning">
                                There is no active lease for this unit. To view older leases select lease from Current/Previous lease(s) dropdown.
                            </p>

                        @endif



                    </div>
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

        <div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <div class="modal-title pt-1"><strong class="photoType">Move-in photo.</strong> Date/Time Uploaded: <span class="timeUploaded"></span></div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-1">
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="container-fluid pt-4">
            <h1 class="h2">Lease</h1>
            <p class="alert alert-warning">
                You don't have any lease yet.
            </p>
        </div>
    @endif
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ url('/') }}/vendor/bootstrap4-editable/css/bootstrap-editable.css">
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.leases-list').change(function(){
                lease_id = $(this).val();
                document.location = "{{ route("tenant/leases") }}" + "?lease_id="+lease_id;
            });
        });
    </script>

    @if ($selectedLease)
    <script>
        $('.moveImageUpload').on('change', function () {
            var form_data = new FormData();
            var fileFieldId = $(this).attr('id');
            var target_list = $(this).data('target_list');
            form_data.append("_token", '{{ csrf_token() }}');
            form_data.append("lease_id", '{{ $selectedLease->id }}');
            form_data.append("unit_id", '{{ $selectedLease->unit_id }}');
            form_data.append("document_type", $(this).data('document_type'));
            var ins = document.getElementById(fileFieldId).files.length;
            var sizes_ok = true;
            var num_uploaded = 0;
            for (var x = 0; x < ins; x++) {
                if(document.getElementById(fileFieldId).files[x].size > 5242880){
                    sizes_ok = false;
                } else {
                    form_data.append("documents[]", document.getElementById(fileFieldId).files[x]);
                    num_uploaded++;
                }
            }
            if((sizes_ok === false) && (num_uploaded === 0)){
                alert("File is too big");
                return;
            }

            var loadingbox =
                '<div class="loadingBox galleryItem">' +
                '<div class="galleryItemContent" style="background: url(/images/loading.gif) no-repeat center #fff">' +
                '</div>' +
                '</div>';
            $(target_list).append(loadingbox);
            $(target_list).parent().removeClass('d-none');

            $.ajax({
                url: '{{ route('leases/move-in-out-upload') }}',
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
                                '<div class="loadingBox galleryItem fileWithError">' +
                                '<div class="galleryItemContent" style="background:#ffacb2">' +
                                '<div class="p-2 text-center text-danger">' + response.uploaded[index].error + '</div>' +
                                '</div>' +
                                '</div>';
                        } else {
                            docbox = docbox +
                                '<a href="' + response.uploaded[index].url + '" class="galleryItem" data-toggle="modal" data-target="#imageModal" data-time_uploaded="' + response.uploaded[index].created_at + '"  data-documentid="' + response.uploaded[index].id + '">' +
                                '<div class="galleryItemContent" style="background-image: url(' + response.uploaded[index].thumb_url + ')">' +
                                '<div class="gallaryControl galleryTrash deleteDocument" data-documentid="' + response.uploaded[index].id + '"><i class="fal fa-trash-alt text-white"></i></div>' +
                                '</div>' +
                                '</a>' +
                                '<input type="hidden" name="document_ids[]" value="' + response.uploaded[index].id + '">';
                        }
                    }
                    $(target_list).append(docbox);
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
                url: '{{ route('leases/document-delete') }}',
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

        $(document).ready(function() {
            $('#imageModal').on('show.bs.modal', function (event) {
                var t = $(event.relatedTarget);
                $('#imageModal').find('.modal-body').html('<img src="' + t.attr('href') + '">');
                $('#imageModal').find('.timeUploaded').html(t.data('time_uploaded'));
                $('#imageModal').find('.photoType').html(t.data('photo_type'));
            });
        });
    </script>
    @endif
@endsection
