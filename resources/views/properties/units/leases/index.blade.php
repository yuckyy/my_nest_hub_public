@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')

    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @include('properties.units.header-partial')

                <div class="leaseFilterToolbar btn-toolbar mb-2 mb-md-0">
                    <div class="input-group input-group-sm mr-0 mr-sm-3">

                        @if (count($unit->leases) > 0)
                        <select class="custom-select custom-select-sm leases-list">
                            <option value="">Current/Previous lease(s)</option>
                            @if (count($activeLeases) > 0)
                                <optgroup label="Current lease">
                                    @foreach ($activeLeases as $lease)
                                        <option value="{{ $lease->id }}" @if($selectedLease && ($selectedLease->id == $lease->id)) selected="selected" @endif>
                                            Lease:&nbsp;
                                            {{ $lease->custom_start_date }}&nbsp;
                                            -&nbsp;
                                            {{ $lease->custom_end_date }}&nbsp;
                                            Tenant: {{ $lease->firstname }} {{ $lease->lastname }}
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
                                            Tenant: {{ $lease->firstname }} {{ $lease->lastname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        @endif
                    </div>

                    @if (!$unit->isOccupied())
                    <a href="{{ route('leases/add', ['unit' => $unit->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fal fa-plus-circle mr-1"></i> Add New
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @if(!empty($selectedLease->deleted_at))
            <div class="container-fluid pb-2">
                <p class="alert alert-danger">
                    The lease is closed and this unit is now vacant. You can create a new Lease by pressing the
                        <a href="{{ route('leases/add', ['unit' => $unit->id]) }}" >
                            "Add New"
                        </a>
                     button.
                </p>
            </div>
        @endif

        <div class="container-fluid unitFormContainer">
            <div class="row">
                @if(!Request::is('properties/leases/*'))
                <div class="navTabsLeftContainer col-md-3">
                    @include('includes.units.menu')
                </div>
                @endif

                <div class="navTabsLeftContent col-md-9 @if (($selectedLease !== false) && empty($selectedLease->deleted_at)) activeLeaseContent @else inactiveLeaseContent @endif">
                    @if (count($unit->leases) > 0)
                        @if ($selectedLease)
                            <div class="card propertyForm mb-4">

                                <div class="row no-gutters">
                                    <div class="col-md-6 bg-light border-right">

                                        <div class="card-header d-flex">
                                            Tenant Information
                                            @if (($selectedLease !== false) && empty($selectedLease->deleted_at) && empty($selectedLease->tenantLastLogin()) )
                                                <div class="ml-1 mr-auto" data-toggle="tooltip" data-placement="top" title="Tenant has not registered. Resend an Invitation Email." style="margin: -5px 0">
                                                    <button class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#confirmResendEmailModal">
                                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>First Name</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                class="editableData"
                                                                id="firstname"
                                                                data-pk="first_name"
                                                                data-type="text"
                                                                data-label="First Name"
                                                            >{!! $selectedLease->firstname !!}</span>

                                                            <i class="fas fa-pencil-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3 editableBlock">
                                                    <label>Last Name</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                class="editableData"
                                                                id="lastname"
                                                                data-pk="last_name"
                                                                data-type="text"
                                                                data-label="Last Name"
                                                            >{!! $selectedLease->lastname !!}</span>

                                                            <i class="fas fa-pencil-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6 editableBlock">
                                                    <label>Email Address</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <a href="mailto:{!! $selectedLease->email !!}"
                                                                class="notEditableData"
                                                                id="email"
                                                                data-pk="email_address"
                                                                data-type="email"
                                                                data-placement="top"
                                                                data-label="Email Address"
                                                            >{!! $selectedLease->email !!}</a>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 editableBlock">
                                                    <label>Phone</label>
                                                    <div class="editableBox">
                                                        <span class="editThis">
                                                            <span
                                                                    class="editableData"
                                                                    id="phone"
                                                                    data-pk="phone"
                                                                    data-type="phone"
                                                                    data-label="Phone"
                                                            >{!! $selectedLease->phone !!}</span>

                                                            <i class="fas fa-pencil-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 bg-light">

                                        <div class="card-header">
                                            Property Information
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3 editableBlock">
                                                <label>Property</label>
                                                <div class="editableBox">
                                                    <span class="editThis">
                                                        <span
                                                            class="notEditableData"
                                                            id="property"
                                                            data-value=""
                                                            data-label="Property"
                                                        >{!! $unit->property->address !!}</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="editableBlock">
                                                <label>Unit</label>
                                                <div class="editableBox">
                                                    <span class="editThis">
                                                        <span
                                                            class="notEditableData"
                                                            id="unit"
                                                            data-pk="unit_id"
                                                            data-placement="top"
                                                            data-value="2"
                                                        >{!! $unit->name !!}</span>
                                                    </span>
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
                                                                @if($startDateEditable)
                                                                    class="editableData"
                                                                @else
                                                                    class="notEditableData"
                                                                @endif
                                                                id="start_date"
                                                                data-pk="start_date"
                                                                data-type="date"
                                                                data-label="Start Date"
                                                            >{!! \Carbon\Carbon::parse($selectedLease->start_date)->format("m/d/Y") !!}</span>
                                                            @if($startDateEditable)
                                                                <i class="fas fa-pencil-alt"></i>
                                                            @endif
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
                                                                data-type="date"
                                                                data-label="End Date"
                                                            >{!! $selectedLease->end_date ? \Carbon\Carbon::parse($selectedLease->end_date)->format("m/d/Y") : 'Month to Month' !!}</span>
                                                            <i class="fas fa-pencil-alt"></i>
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
                                                            <i class="fas fa-pencil-alt"></i>
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
                                                        <i class="fas fa-pencil-alt"></i>
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
                                                                data-alert="Since lease already started, changes of any amounts will be reflected on Payments page next month."
                                                        >{!! financeFormat($selectedLease->late_fee_amount) !!}</span>
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4 border-top d-flex align-items-center">
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
                                                            data-alert="Since lease already started, changes of any amounts will be reflected on Payments page next month."
                                                        >{!! financeFormat($selectedLease->amount) !!}</span>
                                                        <i class="fas fa-pencil-alt"></i>
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
                                                        data-alert="Since lease already started, changes of any amounts will be reflected on Payments page next month."
                                                    >{!! financeFormat($selectedLease->section8) !!}</span>
                                                    <i class="fas fa-pencil-alt"></i>
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
                                                        data-alert="Since lease already started, changes of any amounts will be reflected on Payments page next month."
                                                    >{!! financeFormat($selectedLease->military) !!}</span>
                                                    <i class="fas fa-pencil-alt"></i>
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
                                                    data-alert="Since lease already started, changes of any amounts will be reflected on Payments page next month."
                                                >{!! financeFormat($selectedLease->other) !!}</span>
                                                <i class="fas fa-pencil-alt"></i>
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
                                                        <span class="editableData" id="bill-{!! $bill->id !!}" data-label="{!! $bill->name !!} Bill" data-type="numeric" data-additionaltype="bill" data-placement="top"
                                                              data-alert="Since lease already started, changes of any amounts will be reflected on Payments page next month."
                                                        >{!! financeFormat($bill->value) !!}</span>
                                                        <i class="fas fa-pencil-alt"></i>
                                                        <span class="naData">per month</span>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if (empty($selectedLease->deleted_at))
                                        <button id="addBillButton" class="addRowButton btn btn-outline-secondary btn-sm">
                                            <i class="fal fa-plus-circle mr-1"></i>add another bill
                                        </button>
                                    @endif
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
                                                                data-type="date"
                                                                data-label="Pro-Rated Rent Due"
                                                            >{!! !empty($selectedLease->prorated_rent_due) ? \Carbon\Carbon::parse($selectedLease->prorated_rent_due)->format("m/d/Y") : "Not specified" !!}</span>
                                                            <i class="fas fa-pencil-alt"></i>
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
                                                            <i class="fas fa-pencil-alt"></i>
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
                                                            <i class="fas fa-pencil-alt"></i>
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
                                                                    data-type="date"
                                                                    data-label="Due On"
                                                            >{!! !empty($selectedLease->security_deposit) ? \Carbon\Carbon::parse($selectedLease->security_deposit)->format("m/d/Y") : "Not specified" !!}</span>
                                                            <i class="fas fa-pencil-alt"></i>
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
                                                                <span class="editableData" id="movein_amount-{!! $moveIn->id !!}" data-type="numeric" data-additionaltype="movein-amount" data-label="Move In Amount">{!! financeFormat($moveIn->amount) !!}</span>
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3 editableBlock">
                                                    <label>Due On</label>
                                                    <div class="editableBox">
                                                            <span class="editThis">
                                                                <span class="editableData" id="movein_date-{!! $moveIn->id !!}" data-type="date" data-additionaltype="movein-date" data-label="Move In Due On">{!! \Carbon\Carbon::parse($moveIn->due_on)->format("m/d/Y") !!}</span>
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 mb-3 editableBlock">
                                                    <label>Memo</label>
                                                    <div class="editableBox">
                                                            <span class="editThis">
                                                                <span class="editableData" id="movein_memo-{!! $moveIn->id !!}" data-type="text" data-additionaltype="movein-memo" data-label="Move In Memo">{!! $moveIn->memo !!}</span>
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
                                        <button id="addMoveInCostsButton" class="addRowButton btn btn-outline-secondary btn-sm">
                                            <i class="fal fa-plus-circle mr-1"></i>Add Move-in Cost
                                        </button>
                                    @endif
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
                                                    <i class="fas fa-pencil-alt"></i>
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

                            <div class="card propertyForm mb-4">

                                <div class="card-header">
                                    Share any additional documents
                                </div>
                                <div class="card-body bg-light">
                                    <div class="row">
                                        @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
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
                                                        Upload here the attachments that you want to be visible to your tenants.<br>
                                                        Maximum size: 2Mb.<br>
                                                        Allowed file types: doc, pdf, txt, jpg, png, gif, xls, csv.
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="@if (($selectedLease !== false) && empty($selectedLease->deleted_at)) col-md-8 pl-md-0 @else col @endif @if($selectedLease->sharedDocuments()->count() == 0) d-none @endif ">
                                            <ul id="sharedFileList" class="sharedFileList list-group">
                                                @foreach ($selectedLease->sharedDocuments() as $document)
                                                    <li class="list-group-item list-group-item-action" data-documentid="{{ $document->id }}">
                                                        <a class="sharedFileLink" href="https://docs.google.com/viewer?url=https://portal-rents.web-104.net/storage/{{ $document->filepath }}"
{{--                                                           target="_blank"--}}
                                                        >{!! $document->icon() !!} <span>{{ $document->name }}</span></a>
                                                        @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
                                                            <button class="btn btn-sm btn-cancel deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
                                                            <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </div>
                                </div>

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

                                @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
                                    <a id="end_lease_anchor" name="end_lease_anchor"></a>
                                    <div class="card-footer text-muted">
                                        <h3>End Lease</h3>
                                        <div class="inRowComment">
                                            <i class="fal fa-info-circle"></i> We will immediately notify your tenants and stop all of their upcoming payments. Any payments that have already started will continue to process.
                                            You will still be able to access your previous transactions and tenant history.
                                        </div>

                                        <button class="btn btn-danger btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmEndLeaseModal">
                                            <i class="fal fa-handshake-slash mr-1"></i> End Lease
                                        </button>
                                    </div>
                                @endif
                            </div>

                        @else
                            {{--
                            <!-- there is no "else" we are displaying an inactive lease when we don't have an active one -->
                            <div class="card">
                                <div class="card-body bg-light emptyUnitCard">
                                    <p class="alert alert-warning">
                                        There is no active lease for this unit. To view older leases select lease from Current/Previous lease(s) dropdown.
                                    </p>
                                </div>
                            </div>
                            --}}
                        @endif

                    @else

                        <div class="card">
                            <div class="card-body bg-light emptyUnitCard">
                                <p class="alert alert-warning">
                                    You didn't create any lease yet. Press "Add New" to create new lease.
                                </p>
                            </div>
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>

    @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
        <div class="modal fade" tabindex="-1" role="dialog" id="modal-new-bill">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add another bill</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body bg-light">
                        <div class="alert mb-3 alert-warning">
                            Since lease already started, changes of any amounts will be reflected on Payments page next month.
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="modal-new-bill-name">
                                    Bill Name <i class="required fal fa-asterisk"></i>
                                </label>

                                <input type="text" maxlength="255"
                                    class="form-control"
                                    id="modal-new-bill-name">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modal-new-bill-value">
                                    Monthly amount <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>

                                    <input type="text" value="0.00"
                                        data-type="currency" data-maxamount="999999999" maxlength="20"
                                        class="form-control"
                                        id="modal-new-bill-value">
                                </div>
                            </div>
                        </div>

                        <span style="display: none;" id="modal-new-bill-error" class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="modal-new-bill-save"><i class="fal fa-plus-circle mr-1"></i> Add</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="modal-new-move-in">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Move-in Cost</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body bg-light">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="modal-new-move-in-amount">
                                    Amount <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>

                                    <input type="text" value="0.00"
                                        data-type="currency" data-maxamount="999999999" maxlength="20"
                                        class="form-control move-in-amount"
                                        id="modal-new-move-in-amount">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="modal-new-move-in-due-on">
                                    Due On <i class="required fal fa-asterisk"></i>
                                </label>

                                <input id="modal-new-move-in-due-on"
                                    type="date"
                                    value=""
                                    class="form-control move-in-due">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="modal-new-move-in-memo">
                                    Memo <i class="required fal fa-asterisk"></i>
                                </label>

                                <input type="text"
                                    class="form-control move-in-memo"
                                    id="modal-new-move-in-memo"
                                    maxlength="255">
                            </div>
                        </div>

                        <span style="display: none;" id="modal-new-move-in-error" class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="modal-new-move-in-save"><i class="fal fa-plus-circle mr-1"></i> Add</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="modal-text">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <input type="text" class="form-control" id="modal-text-value" maxlength="127">
                        <select class="form-control" id="modal-text-select-value">
                        </select>
                        <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="modal-text-save"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="modal-numeric">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <input type="text" data-type="currency" data-maxamount="999999999" maxlength="20" class="form-control" id="modal-numeric-value">
                        <span style="display: none;" id="modal-numeric-error" class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="modal-numeric-save"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="modal-date">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <input type="date" class="form-control" id="modal-date-value">
                        <span style="display: none;" id="modal-date-error" class="invalid-feedback  request-error-message" role="alert">
                            <strong></strong>
                        </span>
                        <div id="month_to_month_box" class="d-none custom-control custom-checkbox pt-2 ml-2">
                            <input type="checkbox" id="month_to_month" class="custom-control-input">
                            <label for="month_to_month" class="custom-control-label">Month to Month</label>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" id="modal-date-save"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- END LEASE confirmation dialog-->
        <div class="modal fade" id="confirmEndLeaseModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm End Lease</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <p>Are you sure you would like to end lease with <strong id="modalFirstname">{!! $selectedLease->firstname !!}</strong> <strong id="modalLastname">{!! $selectedLease->lastname !!}</strong>?</p>
                        <p>By pressing End Lease:</p>
                        <ul>
                            <li>We will immediately notify your tenants and stop all of their upcoming payments.</li>
                            <li>Any payments that have already started will continue to process.</li>
                            <li>You will still be able to access your previous transactions and tenant history.</li>
                        </ul>
                        <div>Press cancel to cancel ending lease.</div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fal fa-times mr-1"></i> Cancel
                        </button>
                        <form method="POST" action="{{ route('leases/end') }}">
                            @csrf
                            <input type="hidden" name="lease" value="{{ $selectedLease->id }}">
                            <button class="btn btn-danger btn-sm mr-3" type="submit">
                                <i class="fal fa-handshake-slash mr-1"></i> End Lease
                            </button>
                        </form>
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
                        <p class="mb-0">Do You want to resend an invitation email to <strong id="modalFirstname">{!! $selectedLease->firstname !!}</strong> <strong id="modalLastname">{!! $selectedLease->lastname !!}</strong>?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <form method="POST" action="{{ route('leases/resend-email') }}">
                            @csrf
                            <input type="hidden" name="lease" value="{{ $selectedLease->id }}">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-paper-plane mr-2"></i> Resend an Invitation Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

@endsection

@section('styles')
    <link rel="stylesheet" href="{{ url('/') }}/vendor/bootstrap4-editable/css/bootstrap-editable.css">
@endsection

@section('scripts')
    @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
        <script src='{{ asset('js/validation.js') }}'></script>
        <script src='{{ url('/') }}/vendor/bootstrap4-editable/js/bootstrap-editable.js'></script>
        <script src='{{ url('/') }}/vendor/moment.min.js'></script>
        <script>
            var leaseid = {!! $selectedLease->id ?? -1 !!};
            var name = '';
            var type = 'text';
            var max = -1;
            var min = -1;

            $(document).ready(function() {
                const lease_is_deleted = false;
                const textInputSelector = '#modal-text-value';
                const selectInputSelector = '#modal-text-select-value';
                let inputSelector = textInputSelector;

                const setupInput = (type) => {
                    if (type === 'text') {
                        inputSelector = textInputSelector;
                        $(textInputSelector).show().unmask();
                        $(selectInputSelector).hide();
                    } else if (type === 'select') {
                        inputSelector = selectInputSelector;
                        $(textInputSelector).hide();
                        $(selectInputSelector).show();
                    } else if(type === 'phone'){
                        inputSelector = textInputSelector;
                        $(textInputSelector).show().mask('000-000-0000');
                        $(selectInputSelector).hide();
                    }
                };

                $("#modal-numeric, #modal-new-bill, #modal-new-move-in").find("input[data-type='currency']").on({
                    keyup: function() {
                        formatCurrency($(this));
                    },
                    blur: function() {
                        formatCurrency($(this), "blur");
                    }
                });
                $("#modal-numeric, #modal-new-bill, #modal-new-move-in").find("input[data-maxamount]").on({
                    keyup: function() {
                        restrictMaxAmount($(this));
                    }
                });

                $('.editableData[data-type="text"]').each(function () {
                    var _this = this;
                    $(this).parent().on(
                        'click',
                        function (e) {
                            if (!lease_is_deleted) {
                                name = $(_this).attr('id');
                                if($(_this).data('additionaltype')){
                                    type = $(_this).data('additionaltype');
                                } else {
                                    type = 'string';
                                }
                                $('#modal-text .modal-title').text('Edit ' + $(_this).data('label'));
                                setupInput('text');
                                $(inputSelector).val($(_this).text());
                                $('#modal-text').modal();

                                var modalBody = $('#modal-text').find(".modal-body");
                                modalBody.find(".dynamic-alert").remove();
                                if($(_this).data('alert')){
                                    modalBody.prepend('<div class="dynamic-alert alert mb-3 alert-warning">'+$(_this).data('alert')+'</div>');
                                }
                            }
                        }
                    );
                });

                $('.editableData[data-type="phone"]').each(function () {
                    var _this = this;
                    $(this).parent().on(
                        'click',
                        function (e) {
                            name = $(_this).attr('id');
                            type = 'phone';

                            $('#modal-text .modal-title').text('Edit ' + $(_this).data('label'));
                            setupInput('phone');
                            $(inputSelector).val($(_this).text());
                            $('#modal-text').modal();
                        }
                    );
                });

                $('.editableData[data-type="date"]').each(function () {
                    var _this = this;
                    $(this).parent().on(
                        'click',
                        function (e) {
                            if (!lease_is_deleted) {
                                name = $(_this).attr('id');
                                if($(_this).data('additionaltype')){
                                    type = $(_this).data('additionaltype');
                                } else {
                                    type = 'date';
                                }

                                if( name === 'end_date'){
                                    $('#month_to_month_box').removeClass('d-none');
                                    if( $(_this).text() == 'Month to Month' ){
                                        $('#month_to_month').prop('checked', true);
                                    } else {
                                        $('#month_to_month').prop('checked', false);
                                    }
                                } else {
                                    $('#month_to_month_box').addClass('d-none');
                                }

                                $('#modal-date .modal-title').text('Edit ' + $(_this).data('label'));
                                var [m, d, y] = $(_this).text().split('/');
                                $('#modal-date-value').val(y + '-' + m + '-' + d);
                                $('#modal-date').modal();
                            }
                        }
                    );
                });
                $('#month_to_month').on("change",function(e){
                    if(name === 'end_date') {
                        if ($('#month_to_month').is(':checked')) {
                            $('#modal-date-value').val("");
                        } else {
                            if ($('#end_date').text() != 'Month to Month') {
                                var [m, d, y] = $("#end_date").text().split('/');
                                $('#modal-date-value').val(y + '-' + m + '-' + d);
                            } else {
                                var fullDate = new Date();
                                var twoDigitMonth = fullDate.getMonth()+"";if(twoDigitMonth.length==1)	twoDigitMonth="0" +twoDigitMonth;
                                var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1)	twoDigitDate="0" +twoDigitDate;
                                var currentDate = fullDate.getFullYear() + '-' + twoDigitMonth + "-" + twoDigitDate;
                                $('#modal-date-value').val(currentDate);
                            }
                        }
                    }
                });
                $('#modal-date-value').on("change",function(e){
                    if(name === 'end_date'){
                        if ($('#modal-date-value').val()) {
                            $('#month_to_month').prop('checked', false);
                        } else {
                            $('#month_to_month').prop('checked', true);
                        }
                    }
                });

                $('.editableData[data-type="number"]').each(function () {
                    var _this = this;
                    $(this).parent().on(
                        'click',
                        function (e) {
                            if (!lease_is_deleted) {
                                name = $(_this).attr('id');
                                type = 'integer';
                                max = $(_this).attr('data-max');
                                min = 1;

                                $('#modal-text .modal-title').text('Edit ' + $(_this).data('label'));
                                setupInput('select');
                                // delete old options
                                $(inputSelector).empty();

                                // add new options
                                for (let index = min; index <= max ; index++) {
                                    $(inputSelector).append(
                                        new Option(index.toString(), index.toString()
                                        ));
                                }

                                $(inputSelector).val($(_this).text());
                                $('#modal-text').modal();

                                var modalBody = $('#modal-text').find(".modal-body");
                                modalBody.find(".dynamic-alert").remove();
                                if($(_this).data('alert')){
                                    modalBody.prepend('<div class="dynamic-alert alert mb-3 alert-warning">'+$(_this).data('alert')+'</div>');
                                }
                            }
                        }
                    );
                });

                $('.editableData[data-type="numeric"]').each(function () {
                    var _this = this;
                    $(this).parent().on('click',
                        function (e) {
                            if (!lease_is_deleted) {
                                if($(_this).data('additionaltype')){
                                    type = $(_this).data('additionaltype');
                                } else {
                                    type = 'numeric';
                                }
                                name = $(_this).attr('id');
                                min = 1;

                                $('#modal-numeric .modal-title').text('Edit ' + $(_this).data('label'));
                                $("#modal-numeric-value").val($(_this).text());
                                formatCurrency($("#modal-numeric-value"));
                                $('#modal-numeric').modal();

                                var modalBody = $('#modal-numeric').find(".modal-body");
                                modalBody.find(".dynamic-alert").remove();
                                if($(_this).data('alert')){
                                    modalBody.prepend('<div class="dynamic-alert alert mb-3 alert-warning">'+$(_this).data('alert')+'</div>');
                                }
                            }
                        }
                    );
                });

                $('#modal-text').on('hidden.bs.modal', function () {
                    $('#modal-text-error').css('display', 'none');
                });

                $('#modal-numeric').on('hidden.bs.modal', function () {
                    $('#modal-numeric-error').css('display', 'none');
                });

                $('#modal-date').on('hidden.bs.modal', function () {
                    $('#modal-date-error').css('display', 'none');
                });

                $('#addBillButton').on(
                    'click',
                    function (e) {
                        if (!lease_is_deleted) {
                            type = 'new_bill';

                            $('#modal-new-bill').modal();
                        }
                    }
                );

                $('#addMoveInCostsButton').on(
                    'click',
                    function (e) {
                        if (!lease_is_deleted) {
                            type = 'new_movein';

                            $('#modal-new-move-in').modal();
                        }
                    }
                );

                function setupOnClickHandlersForBills () {
                    $('.bills-container .editableData[data-type="numeric"]').each(function () {
                        var _this = this;
                        $(this).parent().on('click',
                            function (e) {
                                if (!lease_is_deleted) {
                                    name = $(_this).attr('id');
                                    type = 'bill';

                                    $('#modal-numeric .modal-title').text('Edit ' + $(_this).data('label'));
                                    $('#modal-numeric-value').val($(_this).text());
                                    formatCurrency($("#modal-numeric-value"));
                                    $('#modal-numeric').modal();
                                }
                            }
                        );
                    });
                }

                function setupOnClickHandlersForMoveIn () {
                    $('.move-ins-container .editableData[data-type="numeric"]').each(function () {
                        var _this = this;
                        $(this).parent().on('click',
                            function (e) {
                                if (!lease_is_deleted) {
                                    name = $(_this).attr('id');
                                    type = 'movein-amount';

                                    $('#modal-numeric .modal-title').text('Edit ' + $(_this).data('label'));
                                    $('#modal-numeric-value').val($(_this).text());
                                    formatCurrency($("#modal-numeric-value"));
                                    $('#modal-numeric').modal();
                                }
                            }
                        );
                    });

                    $('.move-ins-container .editableData[data-type="text"]').each(function () {
                        var _this = this;
                        $(this).parent().on(
                            'click',
                            function (e) {
                                if (!lease_is_deleted) {
                                    name = $(_this).attr('id');
                                    type = 'movein-memo';

                                    $('#modal-text .modal-title').text('Edit ' + $(_this).data('label'));
                                    setupInput('text');
                                    $(inputSelector).val($(_this).text());
                                    $('#modal-text').modal();
                                }
                            }
                        );
                    });

                    {{--
                    $('.move-ins-container .editableData[data-type="text"]').each(function () {
                        var _this = this;
                        $(this).parent().on(
                            'click',
                            function (e) {
                                if (!lease_is_deleted) {
                                    name = $(_this).attr('id');
                                    type = 'movein-memo';

                                    $('#modal-text .modal-title').text('Edit ' + $(_this).data('label'));
                                    setupInput('text');
                                    $(inputSelector).val($(_this).text());
                                    $('#modal-text').modal();
                                }
                            }
                        );
                    });
                    --}}

                    $('.move-ins-container .editableData[data-type="date"]').each(function () {
                        var _this = this;
                        $(this).parent().on(
                            'click',
                            function (e) {
                                if (!lease_is_deleted) {
                                    name = $(_this).attr('id');
                                    type = 'movein-date';

                                    $('#modal-date .modal-title').text('Edit ' + $(_this).data('label'));
                                    var [m, d, y] = $(_this).text().split('/');
                                    $('#modal-date-value').val(y + '-' + m + '-' + d);
                                    $('#modal-date').modal();
                                }
                            }
                        );
                    });
                }

                function appendBillHtml (bill, billKey) {
                    $($('.bills-container')[0]).append(
                        '<div class="col-md-4 mb-3 editableBlock">' +
                        '<label>' + bill.name + '</label>' +
                        '<div class="editableBox">' +
                        '<span class="editThis">' +
                        '<span class="naData">$</span>' +
                        '<span class="editableData" data-label="' + bill.name + ' Bill" id="bill-' + (bill.id || billKey) + '" data-type="numeric" data-placement="top">' + bill.value + '</span>' +
                        '<i class="fas fa-pencil-alt"></i> ' +
                        '<span class="naData">per month</span>' +
                        '</span>' +
                        '</div>' +
                        '</div>'
                    );
                }

                function appendMoveInHtml (moveIn, moveInKey) {
                    $($('.move-ins-container')[0]).append(
                        '<div class="form-row">' +
                        '    <div class="col-md-3 mb-3 editableBlock">' +
                        '        <label>Amount</label>' +
                        '        <div class="editableBox">' +
                        '        <span class="editThis">' +
                        '            <span class="naData">$</span>' +
                        '            <span class="editableData" id="movein_amount-' + (moveIn.id || moveInKey) + '" data-type="numeric" data-label="Move In Amount">' + moveIn.amount + '</span>' +
                        '            <i class="fas fa-pencil-alt"></i>' +
                        '        </span>' +
                        '        </div>' +
                        '    </div>' +
                        '    <div class="col-md-4 mb-3 editableBlock">' +
                        '        <label>Due On</label>' +
                        '        <div class="editableBox">' +
                        '        <span class="editThis">' +
                        '            <span class="editableData" id="movein_date-' + (moveIn.id || moveInKey) + '" data-type="date" data-label="Move In Due On">' + moveIn.due_on + '</span>' +
                        '            <i class="fas fa-pencil-alt"></i>' +
                        '        </span>' +
                        '        </div>' +
                        '    </div>' +
                        '    <div class="col-md-5 mb-3 editableBlock">' +
                        '        <label>Memo</label>' +
                        '        <div class="editableBox">' +
                        '        <span class="editThis">' +
                        '            <span class="editableData" id="movein_memo-' + (moveIn.id || moveInKey) + '" data-type="text" data-label="Move In Memo">' + moveIn.memo + '</span>' +
                        '            <i class="fas fa-pencil-alt"></i>' +
                        '        </span>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>'
                    );
                }

                $('#modal-new-bill-save').on(
                    'click',
                    function () {
                        const billName = $('#modal-new-bill-name').val();

                        if (!billName) {
                            $('#modal-new-bill-error').css('display', 'block');
                            $($('#modal-new-bill-error strong')[0]).text(
                                'The bill name field is required.'
                            );

                            return 0;
                        }
                        $(".preloader").fadeIn("slow");
                        $.ajax({
                            url: "{!! route('leases/edit-save', ['unit' => $unit->id]) !!}",
                            processData: true,
                            type: 'POST',
                            data: {
                                '_token': '{!! csrf_token() !!}',
                                lease: leaseid,
                                name: billName,
                                value: $('#modal-new-bill-value').val(),
                                type: type
                            },
                            success: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-new-bill-name').val('');
                                $('#modal-new-bill-value').val('0.00');
                                $('#modal-new-bill-error').css('display', 'none');

                                appendBillHtml({
                                    ...data.update.bill,
                                    value: data.formatted_amount
                                });

                                setupOnClickHandlersForBills();

                                $('#modal-new-bill').modal('hide');
                            },
                            error: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-new-bill-error').css('display', 'block');
                                $($('#modal-new-bill-error strong')[0]).text(data.responseJSON.errors.value[0]);
                            },
                        });
                    }
                );

                $('#modal-new-move-in-save').on(
                    'click',
                    function () {
                        const memo = $('#modal-new-move-in-memo').val();
                        const due_on = $('#modal-new-move-in-due-on').val();
                        const amount = $('#modal-new-move-in-amount').val();

                        if (!memo || !due_on) {
                            $('#modal-new-move-in-error').css('display', 'block');
                            let text;

                            if (!memo && !due_on) {
                                text = 'The memo and due fields is required.';
                            } else if (!memo) {
                                text = 'The memo field is required.';
                            } else {
                                text = 'The due field is required.';
                            }

                            $($('#modal-new-move-in-error strong')[0]).text(text);

                            return 0;
                        }
                        $(".preloader").fadeIn("slow");
                        $.ajax({
                            url: "{!! route('leases/edit-save', ['unit' => $unit->id]) !!}",
                            processData: true,
                            type: 'POST',
                            data: {
                                '_token': '{!! csrf_token() !!}',
                                lease: leaseid,
                                amount,
                                due_on,
                                memo,
                                type: type,
                            },
                            success: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-new-move-in-memo').val('');
                                $('#modal-new-move-in-due-on').val('');
                                $('#modal-new-move-in-amount').val('0.00');
                                $('#modal-new-move-in-error').css('display', 'none');

                                appendMoveInHtml({
                                    ...data.update.movein,
                                    amount: data.formatted_amount
                                });

                                setupOnClickHandlersForMoveIn();

                                $('#modal-new-move-in').modal('hide');
                            },
                            error: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-new-move-in-error').css('display', 'block');
                                $($('#modal-new-move-in-error strong')[0]).text(data.responseJSON.errors.value[0]);
                            },
                        });
                    }
                );

                $('#modal-text-save').on(
                    'click',
                    function () {
                        $(".preloader").fadeIn("slow");
                        $.ajax({
                            url: "{!! route('leases/edit-save', ['unit' => $unit->id]) !!}",
                            processData: true,
                            type: 'POST',
                            data: {
                                '_token': '{!! csrf_token() !!}',
                                lease: leaseid,
                                name: name,
                                value: $(inputSelector).val(),
                                type: type,
                                max: max,
                                min: min
                            },
                            success: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#' + data.name).text($(inputSelector).val()).addClass("flashit");
                                setTimeout('$(".flashit").removeClass("flashit")',2000);
                                $('#modal-text').modal('hide');
                            },
                            error: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-text-error').css('display', 'block');
                                $($('#modal-text-error strong')[0]).text(data.responseJSON.errors.value[0]);
                            }
                        });
                    }
                );

                $('#modal-numeric-save').on(
                    'click',
                    function () {
                        $(".preloader").fadeIn("slow");
                        $.ajax({
                            url: "{!! route('leases/edit-save', ['unit' => $unit->id]) !!}",
                            processData: true,
                            type: 'POST',
                            data: {
                                '_token': '{!! csrf_token() !!}',
                                lease: leaseid,
                                name: name,
                                value: $("#modal-numeric-value").val(),
                                type: type,
                                max: max,
                                min: min
                            },
                            success: function (data) {
                                $(".preloader").fadeOut("fast");
                                if(data.error == 'error'){
                                    $('#modal-numeric-error').css('display', 'block');
                                    $($('#modal-numeric-error')[0]).text(data.message);
                                } else {
                                    $('#' + data.name).text($("#modal-numeric-value").val()).addClass("flashit");
                                    setTimeout('$(".flashit").removeClass("flashit")',2000);
                                    $('#modal-numeric').modal('hide')
                                }
                            },
                            error: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-numeric-error').css('display', 'block');
                                $($('#modal-numeric-error')[0]).text(data.responseJSON.errors.value[0]);
                            }
                        });
                    }
                );

                $('#modal-date-save').on(
                    'click',
                    function () {
                        $(".preloader").fadeIn("slow");
                        $.ajax({
                            url: '{!! route('leases/edit-save', ['unit' => $unit->id]) !!}',
                            type: 'POST',
                            data: {
                                '_token': '{!! csrf_token() !!}',
                                lease: leaseid,
                                name: name,
                                value: $('#modal-date-value').val(),
                                type: type
                            },
                            success: function (data) {
                                $(".preloader").fadeOut("fast");
                                if(data.error == 'error'){
                                    $('#modal-date-error').css('display', 'block');
                                    $($('#modal-date-error')[0]).text(data.message);
                                } else {
                                    var date = data.value && data.value.split('-');
                                    $('#' + data.name).text(data.value ? date[1] + '/' + date[2] + '/' + date[0] : 'Month to Month').addClass("flashit");
                                    setTimeout('$(".flashit").removeClass("flashit")',2000);
                                    $('#modal-date').modal('hide');
                                }
                            },
                            error: function (data) {
                                $(".preloader").fadeOut("fast");
                                $('#modal-date-error').css('display', 'block');
                                $($('#modal-date-error')[0]).text(data.responseJSON.errors.value[0]);
                            },
                        });
                    }
                );

                $("#{{ $updatedElement }}").addClass("flashit");
                setTimeout('$(".flashit").removeClass("flashit")',2000);

                $('#monthToMonth').change(function() {
                    $('#endDate').prop('disabled', this.checked);
                });
                $('.yesNoSwitch').find('input').change(function() {
                    var collapsedContent = $(this).parents('.card-header').first().next('.collapse').first();
                    if(this.checked){
                        collapsedContent.collapse('show')
                    } else {
                        collapsedContent.collapse('hide')
                    }
                });
                $('.yesNoSwitch').find('input').prop("checked", false);

                $('.addRowButton').click(function(e){
                    e.preventDefault();
                    var n = $(this).data('n');
                    n++;
                    $(this).data('n',n);
                    var target = $(this).data('target');

                    var targrtbox = $('#'+target);
                    var row = targrtbox.find('.rowTemplate').clone();
                    row.removeClass('rowTemplate');
                    row.find('input').each(function(){
                        var replid = $(this).attr('id').replace("0", n);
                        $(this).attr('id',replid);
                    });
                    row.find('label').each(function(){
                        var replfor = $(this).attr('for').replace("0", n);
                        $(this).attr('for',replfor);
                    });
                    row.find('.removeFormRowCell').find('a').click(function(e){
                        e.preventDefault();
                        $(this).parent().parent('.form-row').remove();
                    });
                    targrtbox.append(row);
                });

                $('.leases-list').change(function(){
                    lease_id = $(this).val();
                    document.location = "{{ route("properties/units/leases", ['unit' => $unit->id]) }}" + "?lease_id="+lease_id;
                });
            });

            {{--
            $(document).ready(function() {
                if ($("#financeAccount").val() === "_new"){
                    $("#addFinanceAccountContent").collapse('show');
                } else {
                    $("#addFinanceAccountContent").collapse('hide');
                }

                $("#financeAccount").change(function(){
                    var val = $(this).val();
                    if (val === "_new"){
                        $("#addFinanceAccountContent").collapse('show');
                    } else {
                        $("#addFinanceAccountContent").collapse('hide');
                    }
                });
                $("#cancelCreateFinanceAccount").click(function(e){
                    e.preventDefault();
                    $("#addFinanceAccountContent").collapse('hide');
                });
            });
            --}}
        </script>
        <script>
            jQuery(document).ready(function() {
                $('#documentUpload').on('change', function () {
                    var form_data = new FormData();
                    form_data.append("_token", '{{ csrf_token() }}');
                    form_data.append("lease_id", '{{ $selectedLease->id }}');
                    form_data.append("unit_id", '{{ $selectedLease->unit_id }}');
                    var ins = document.getElementById('documentUpload').files.length;
                    var sizes_ok = true;
                    var num_uploaded = 0;
                    for (var x = 0; x < ins; x++) {
                        if(document.getElementById('documentUpload').files[x].size > 2000000){
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
                        url: '{{ route('leases/document-upload') }}',
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

            });
        </script>
    @else
        <script>
            $(document).ready(function() {
                $('.leases-list').change(function(){
                    lease_id = $(this).val();
                    document.location = "{{ route("properties/units/leases", ['unit' => $unit->id]) }}" + "?lease_id="+lease_id;
                });
            });
        </script>
    @endif
    @if ($selectedLease !== false)
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
