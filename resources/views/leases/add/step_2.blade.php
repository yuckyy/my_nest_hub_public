@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="#">Lease</a> > <a href="#">Create Lease</a>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-md-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <h1 class="h2 text-center text-sm-left">Create Lease</h1>
                <ul id="progressbar">
                    <li class="active progress1"><div>Tenant and Property</div><div>Information</div></li>
                    <li class="active progress2"><div>Extra Fees and</div><div>Assistance</div></li>
                    <li class="progress3"><div>Payments and</div><div>Documents</div></li>
                </ul>
            </div>
        </div>

        <div class="container-fluid">

            <form class="checkUnloadByDefoult" novalidate method="POST" action="{{ route('leases/add') }}">
                @csrf

                <input type="hidden" name="step" id="step" value="{{ $step }}">

                <div class="card propertyForm">
                    <div class="card-header border-top cardHeaderMulti">
                        Fees and Assistance
                    </div>
                    <div class="card-body pb-1">

                        <div class="card mb-3 leaseSectionExpandForm">
                            <div class="card-header bg-light border-0">
                                <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                                    <div>Automatic Late Fees</div>
                                    <div class="btn-group align-items-center">
                                        <label for="automaticLateFees">No</label>
                                        <div class="custom-control custom-switch yesNoSwitch">
                                            <input
                                                    type="checkbox"
                                                    class="custom-control-input {{ !empty(old('automaticLateFees')) || !empty($data['lateFeeAmount']) ? "checked-checkbox" : "" }}"
                                                    id="automaticLateFees"
                                                    name="automaticLateFees"
                                                    {{ !empty(old('automaticLateFees')) || !empty($data['lateFeeAmount']) ? "checked" : "" }}
                                            >

                                            <label class="custom-control-label" for="automaticLateFees"></label>
                                        </div>
                                        <label for="automaticLateFees">Yes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse multi-collapse {{ !empty(old('automaticLateFees')) || !empty($data['lateFeeAmount']) ? "show" : "" }}">
                                <div class="card-body bg-light border-top">
                                    <div class="inRowComment"><i class="fal fa-info-circle"></i> A late fee will be added if payments for the unpaid balance haven’t started sending by the day you specify. We’ll let your tenants know, and they’ll need to schedule a new payment to cover the fee.</div>
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="afterDueDate">Add Fee <i class="required fal fa-asterisk"></i></label>

                                            <div class="input-group">
                                                <select name="afterDueDate" id="afterDueDate" class="form-control @error('afterDueDate') is-invalid @enderror">
                                                    @for($day = 1; $day <= 31; $day++)
                                                        <option value="{{ $day . '' }}" {{ (old('afterDueDate') && (old('afterDueDate') == $day)) || (empty(old('afterDueDate')) && !empty($data['afterDueDate']) && ($data['afterDueDate'] == $day) ) ? 'selected' : "" }}>{{ $day }}</option>
                                                    @endfor
                                                </select>

                                                <div class="input-group-append">
                                                    <span class="input-group-text">day after rent is due</span>
                                                </div>

                                                @error('afterDueDate')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="lateFeeAmount">
                                                Late Fee Amount <i class="required fal fa-asterisk"></i>
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>

                                                <input
                                                        id="lateFeeAmount"
                                                        name="lateFeeAmount"
                                                        type="text"
                                                        maxlength="14"
                                                        data-type="currency"
                                                        data-maxamount="999999"
                                                        class="form-control @error('lateFeeAmount') is-invalid @enderror"
                                                        value="{{ old('lateFeeAmount') ?? $data['lateFeeAmount'] ?? '' }}"
                                                >

                                                @error('lateFeeAmount')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3 leaseSectionExpandForm">
                            <div class="card-header bg-light border-0">
                                <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                                    <div>Collect Rent Assistance Payments</div>
                                    <div class="btn-group align-items-center">
                                        <label for="collectRentAssistance">No</label>
                                        <div class="custom-control custom-switch yesNoSwitch">
                                            <input type="checkbox"
                                                   class="custom-control-input {{ !empty(old('collectRentAssistance')) || !empty($data['section8']) || !empty($data['military']) || !empty($data['other']) ? "checked-checkbox" : "" }}"
                                                   id="collectRentAssistance"
                                                   name="collectRentAssistance"
                                                   {{ !empty(old('collectRentAssistance')) || !empty($data['section8']) || !empty($data['military']) || !empty($data['other']) ? "checked" : "" }}
                                            >
                                            <label class="custom-control-label" for="collectRentAssistance"></label>
                                        </div>
                                        <label for="collectRentAssistance">Yes</label>
                                    </div>
                                </div>
                            </div>

                            <div class="collapse multi-collapse {{ !empty(old('collectRentAssistance')) || !empty($data['section8']) || !empty($data['military']) || !empty($data['other']) ? "show" : "" }}">
                                <div class="card-body bg-light border-top">
                                    <div class="form-row">
                                        <div class="col-md-4">
                                            <label for="rentAssistance1">Section 8</label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="rentAssistance1"
                                                    maxlength="14"
                                                    data-type="currency"
                                                    data-maxamount="999999"
                                                    value="{{ old('section8') ?? $data['section8'] ?? '' }}"
                                                    name="section8"
                                                >

                                                <div class="input-group-append">
                                                    <div class="input-group-text">per month</div>
                                                </div>
                                            </div>
                                            @error('section8')
                                                <span style="display: block;" class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="rentAssistance2">Military Discount</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="rentAssistance2"
                                                    maxlength="14"
                                                    data-type="currency"
                                                    data-maxamount="999999"
                                                    value="{{ old('military') ?? $data['military'] ?? '' }}"
                                                    name="military"
                                                >

                                                <div class="input-group-append">
                                                    <div class="input-group-text">per month</div>
                                                </div>
                                                @error('military')
                                                <span style="display: block;" class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="rentAssistance3">Other</label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">$</div>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="rentAssistance3"
                                                    maxlength="14"
                                                    data-type="currency"
                                                    data-maxamount="999999"
                                                    value="{{ old('other') ?? $data['other'] ?? '' }}"
                                                    name="other"
                                                >

                                                <div class="input-group-append">
                                                    <div class="input-group-text">per month</div>
                                                </div>
                                                @error('other')
                                                <span style="display: block;" class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3 leaseSectionExpandForm">
                            <div class="card-header bg-light border-0">
                                <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                                    <div>Collect Bills</div>
                                    <div class="btn-group align-items-center">
                                        <label for="collectBills">No</label>
                                        <div class="custom-control custom-switch yesNoSwitch">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input {{--
                                                                               !empty(old('collectBills')) ||
                                                                               ($data && !empty($data['defaultBill']) && !empty(array_filter($data['defaultBill']))) ||
                                                                               ($data && !empty($data['bill']) && !empty(array_filter($data['bill'])))
                                                                                ? "checked-checkbox" : ""
                                                                           --}}"
                                                id="collectBills"
                                                name="collectBills"
                                                {{
                                                   !empty(old('collectBills')) ||
                                                   ($data && !empty($data['defaultBill']) && !empty(array_filter($data['defaultBill']))) ||
                                                   ($data && !empty($data['bill']) && !empty(array_filter($data['bill'])))
                                                   ? "checked" : ""
                                                }}
                                               >
                                            <label class="custom-control-label" for="collectBills"></label>
                                        </div>
                                        <label for="collectBills">Yes</label>
                                    </div>
                                </div>
                            </div>

                            <div class="collapse multi-collapse {{
                                                                   !empty(old('collectBills')) ||
                                                                   ($data && !empty($data['defaultBill']) && !empty(array_filter($data['defaultBill']))) ||
                                                                   ($data && !empty($data['bill']) && !empty(array_filter($data['bill'])))
                                                                    ? "show" : ""
                                                                }}">
                                <div class="card-body bg-light border-top">

                                    <div class="inRowComment">
                                        <i class="fal fa-info-circle"></i> These are fixed amount (s) which will be applied every month as an addition to the lease. You will have an ability to add not fixed bills in Payments section.
                                    </div>

                                    <div class="form-row mb-3">

                                        @foreach ($defaultBills as $item)
                                            <div class="col-md-4">
                                                <label for="collectBills1">{{ $item->name }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>

                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="collectBills1"
                                                        name="defaultBill[{{ $item->id }}]"
                                                        maxlength="14"
                                                        data-type="currency"
                                                        data-maxamount="999999"
                                                        value="{{ old('defaultBill.' . $item->id) ?? $data['defaultBill'][$item->id] ?? '' }}"
                                                    >

                                                    <div class="input-group-append">
                                                        <div class="input-group-text">per month</div>
                                                    </div>
                                                </div>
                                                @error('defaultBill.' . $item->id)
                                                    <span style="display: block;" class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>

                                        @endforeach
                                    </div>

                                    <div id="addBillItemsBox" class="addRowBox">

                                        <div class="form-row rowTemplate"><!-- for programming add css .rowTemplate{display:none} -->
                                            <div class="col-md-6 mb-3">
                                                <label for="addBillName0">
                                                    Bill Name <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control bill-name"
                                                    name="bill[][name]"
                                                    id="addBillName0"
                                                    disabled="disabled"
                                                    maxlength="255"
                                                >
                                            </div>

                                            <div class="col-md-5 mb-3">
                                                <label for="addBillAmount0">
                                                    Monthly amount <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>

                                                    <input
                                                        type="text"
                                                        class="form-control bill-amount"
                                                        name="bill[][amount]"
                                                        id="addBillAmount0"
                                                        maxlength="14"
                                                        data-type="currency"
                                                        data-maxamount="999999"
                                                        disabled="disabled"
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-md-1 mb-3 removeFormRowCell">
                                                <a href="#">remove <i class="fal fa-times"></i></a>
                                            </div>
                                        </div>

                                        @php
                                            $bills = old('bill') ?? $data['bill'] ?? null
                                        @endphp
                                        @if (!empty($bills))
                                            @foreach($bills as $billKey => $bill)
                                                @if ($billKey == 0) @continue
                                                @endif
                                                <div class="form-row"><!-- for programming add css .rowTemplate{display:none} -->
                                                    <div class="col-md-6 mb-3">
                                                        <label for="addBillName"{{ $billKey }}>
                                                            Bill Name <i class="required fal fa-asterisk"></i>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control bill-name @error('bill.'. $billKey. '.name') is-invalid @enderror"
                                                            name="bill[{{ $billKey }}][name]"
                                                            id="addBillName"{{ $billKey }}
                                                            value="{{ !empty($bill['name']) ? $bill['name'] : "" }}"
                                                            required
                                                        >
                                                        @error('bill.'. $billKey. '.name')
                                                            <span class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-5 mb-3">
                                                        <label for="addBillAmount0">
                                                            Monthly amount <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">$</div>
                                                            </div>

                                                            <input
                                                                type="text"
                                                                class="form-control bill-amount @error('bill.'. $billKey. '.amount') is-invalid @enderror"
                                                                name="bill[{{ $billKey }}][amount]"
                                                                id="addBillAmount"{{ $billKey }}
                                                                maxlength="14"
                                                                data-type="currency"
                                                                data-maxamount="999999"
                                                                value="{{ !empty($bill['amount']) ? $bill['amount'] : "" }}"
                                                                required
                                                            >

                                                            @error('bill.'. $billKey. '.amount')
                                                                <span class="invalid-feedback" role="alert">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 mb-3 removeFormRowCell">
                                                        <a href="#">remove <i class="fal fa-times"></i></a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="addBillBox mb-3">
                                        <button
                                            id="addBillButton"
                                            data-n={{!empty($billKey) ? $billKey : "0"}}
                                            data-target="addBillItemsBox"
                                            class="addRowButton btn btn-outline-secondary btn-sm"
                                        >
                                            <i class="fal fa-plus-circle mr-1"></i>add another bill
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card mb-3 leaseSectionExpandForm">
                            <div class="card-header bg-light border-0">
                                <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                                    <div>Collect Prorated Rent</div>
                                    <div class="btn-group align-items-center">
                                        <label for="collectProratedRent">No</label>
                                        <div class="custom-control custom-switch yesNoSwitch">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input  {{ !empty(old('collectProratedRent')) || !empty($data['proratedRentAmount']) ? "checked-checkbox" : "" }}"
                                                id="collectProratedRent"
                                                name="collectProratedRent"
                                                {{ !empty(old('collectProratedRent')) || !empty($data['proratedRentAmount']) ? "checked" : "" }}
                                            >
                                            <label class="custom-control-label" for="collectProratedRent"></label>
                                        </div>
                                        <label for="collectProratedRent">Yes</label>
                                    </div>
                                </div>
                            </div>

                            <div class="collapse multi-collapse {{ !empty(old('collectProratedRent')) || !empty($data['proratedRentAmount']) ? "show" : "" }}" id="collectRentAssistanceContent">
                                <div class="card-body bg-light border-top">
                                    <div class="inRowComment">
                                        <i class="fal fa-info-circle"></i>
                                        If your tenants are moving in early, you can collect a prorated rent payment for the first month.
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="proratedRentDue">
                                                Pro-Rated Rent Due
                                            </label>

                                            <input
                                                name="proratedRentDue"
                                                id="proratedRentDue"
                                                type="date"
                                                value="{{ old('proratedRentDue') ?? '' }}"
                                                class="form-control @error('proratedRentDue') is-invalid @enderror"
                                            >

                                            @error('proratedRentDue')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="proratedRentAmount">
                                                Pro-Rated Rent Amount <i class="required fal fa-asterisk"></i>
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>

                                                <input
                                                    id="proratedRentAmount"
                                                    name="proratedRentAmount"
                                                    type="text"
                                                    maxlength="14"
                                                    data-type="currency"
                                                    data-maxamount="999999"
                                                    class="form-control @error('proratedRentAmount') is-invalid @enderror"
                                                    value="{{ old('proratedRentAmount') ?? $data['proratedRentAmount'] ?? '' }}"
                                                >

                                                @error('proratedRentAmount')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-header border-top cardHeaderMulti">
                        Security Deposit and Move-in Costs
                    </div>

                    <div class="card-body pb-1">

                        <div class="card mb-3 leaseSectionExpandForm">
                            <div class="card-header bg-light border-0">
                                <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                                    <div>Do you need to collect a deposit(s)?</div>
                                    <div class="btn-group align-items-center">
                                        <label for="collectSecurityDeposit">No</label>
                                        <div class="custom-control custom-switch yesNoSwitch">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input  {{
                                                        !empty(old('collectSecurityDeposit')) ||
                                                        !empty($data['securityDepositAmount']) ||
                                                        ($data && !empty($data['moveIn']) && !empty(array_filter($data['moveIn'][0])))
                                                        ? "checked-checkbox" : ""
                                                }}"
                                                id="collectSecurityDeposit"
                                                name="collectSecurityDeposit"
                                                {{
                                                    !empty(old('collectSecurityDeposit')) ||
                                                    !empty($data['securityDepositAmount']) ||
                                                    ($data && !empty($data['moveIn']) && !empty(array_filter($data['moveIn'][0])))
                                                    ? "checked" : ""
                                                }}
                                            >

                                            <label class="custom-control-label" for="collectSecurityDeposit"></label>
                                        </div>
                                        <label for="collectSecurityDeposit">Yes</label>
                                        @error('collectSecurityDeposit')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="collapse multi-collapse {{
                                            !empty(old('collectSecurityDeposit')) ||
                                            !empty($data['securityDepositAmount']) ||
                                            ($data && !empty($data['moveIn']) && !empty(array_filter($data['moveIn'][0])))
                                            ? "show" : ""
                                        }}">
                                <div class="card-body bg-light border-top">
                                    <div class="form-row">
                                        <div class="col-md-5 mb-3">
                                            <label for="securityDepositAmount">
                                                Security Deposit
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>

                                                <input
                                                    id="securityDepositAmount"
                                                    name="securityDepositAmount"
                                                    type="text"
                                                    maxlength="14"
                                                    data-type="currency"
                                                    data-maxamount="999999"
                                                    class="form-control @error('securityDepositAmount') is-invalid @enderror"
                                                    value="{{ old('securityDepositAmount') ?? $data['securityDepositAmount'] ?? '' }}"
                                                >

                                                @error('securityDepositAmount')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="securityDueOn">
                                                Due On
                                            </label>

                                            <input
                                                name="securityDueOn"
                                                id="securityDueOn"
                                                type="date"
                                                class="form-control @error('securityDueOn') is-invalid @enderror"
                                                value="{{ old('securityDueOn') ?? $data['securityDueOn']  ?? '' }}"
                                            >

                                            @error('securityDueOn')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div id="addMoveInCostsBox" class="addRowBox">
                                        <div class="form-row rowTemplate">
                                            <div class="col-md-5 mb-3">
                                                <label for="moveInCostAmount0">
                                                    Move-in Cost <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>

                                                    <input
                                                            type="text"
                                                            class="form-control move-in-amount"
                                                            name="moveIn[0][amount]"
                                                            id="moveInCostAmount0"
                                                            maxlength="14"
                                                            data-type="currency"
                                                            data-maxamount="999999"
                                                    >
                                                    @error('moveIn.0.amount')
                                                    <span class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="moveInCostDueOn0"s>
                                                    Due On <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <input
                                                        name="moveIn[0][due]"
                                                        id="moveInCostDueOn0"
                                                        type="date"
                                                        value=""
                                                        class="form-control move-in-due"
                                                >
                                                @error('moveIn.0.due')
                                                <span class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-11 mb-3">
                                                <label for="moveInCostMemo0">
                                                    Move-in Cost Memo <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <input
                                                        type="text"
                                                        class="form-control move-in-memo"
                                                        name="moveIn[0][memo]"
                                                        id="moveInCostMemo0"
                                                >
                                                @error('moveIn.0.memo')
                                                <span class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-1 mb-3 removeFormRowCell">
                                                <a href="#">remove <i class="fal fa-times"></i></a>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-5 mb-3">
                                                <label for="moveInCostAmount1">
                                                    Move-in Cost
                                                </label>

                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>

                                                    <input
                                                            type="text"
                                                            class="form-control move-in-amount @error('moveIn.0.amount') is-invalid @enderror"
                                                            name="moveIn[0][amount]"
                                                            id="moveInCostAmount1"
                                                            maxlength="14"
                                                            data-type="currency"
                                                            data-maxamount="999999"
                                                            value="{{ !empty(old('moveIn.0.amount')) ? old('moveIn.0.amount') : (!empty($data['moveIn'][0]) ? $data['moveIn'][0]['amount'] : '') }}"
                                                    >
                                                </div>
                                                @error('moveIn.0.amount')
                                                <span style="display: block;" class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="moveInCostDueOn1">
                                                    Due On
                                                </label>

                                                <input
                                                        name="moveIn[0][due]"
                                                        id="moveInCostDueOn1"
                                                        type="date"
                                                        value="{{ !empty(old('moveIn.0.due')) ? old('moveIn.0.due') : (!empty($data['moveIn'][0]) ? $data['moveIn'][0]['due'] : '') }}"
                                                        class="form-control move-in-due @error('moveIn.0.due') is-invalid @enderror"
                                                >
                                                @error('moveIn.0.due')
                                                <span  style="display: block;" class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-11 mb-3">
                                                <label for="moveInCostMemo1">
                                                    Move-in Cost Memo
                                                </label>

                                                <input
                                                        type="text"
                                                        class="form-control move-in-memo @error('moveIn.0.memo') is-invalid @enderror"
                                                        name="moveIn[0][memo]"
                                                        id="moveInCostMemo1"
                                                        value="{{ !empty(old('moveIn.0.memo')) ? old('moveIn.0.memo') : (!empty($data['moveIn'][0]) ? $data['moveIn'][0]['memo'] : '') }}"
                                                >
                                                @error('moveIn.0.memo')
                                                <span  style="display: block;" class="invalid-feedback" role="alert">
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                            {{--<div class="col-md-1 mb-3 removeFormRowCell">
                                                <a href="#">remove <i class="fal fa-times"></i></a>
                                            </div>--}}
                                        </div>

                                        @php
                                            $moveIns = old('moveIn') ?? $data['moveIn'] ?? null
                                        @endphp
                                        @if (!empty($moveIns))
                                            @foreach($moveIns as $miKey => $moveIn)
                                                @if ($miKey == 0) @continue
                                                @endif
                                                <div class="form-row">
                                                    <div class="col-md-5 mb-3">
                                                        <label for="moveInCostAmount{{ $miKey }}">
                                                            Move-in Cost <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">$</div>
                                                            </div>
                                                            <input
                                                                    type="text"
                                                                    class="form-control move-in-amount @error('moveIn.' . $miKey . '.amount') is-invalid @enderror"
                                                                    name="moveIn[{{ $miKey }}][amount]"
                                                                    id="moveInCostAmount{{ $miKey }}"
                                                                    maxlength="14"
                                                                    data-type="currency"
                                                                    data-maxamount="999999"
                                                                    value="{{!empty($moveIn['amount']) ? $moveIn['amount'] : ""}}"
                                                            >
                                                            @error('moveIn.' . $miKey . '.amount')
                                                            <span style="display: block;" class="invalid-feedback" role="alert">
                                                                {{ $message }}
                                                            </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="moveInCostDueOn{{ $miKey }}">
                                                            Due On <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <input
                                                                name="moveIn[{{ $miKey }}][due]"
                                                                id="moveInCostDueOn{{ $miKey }}"
                                                                type="date"
                                                                value="{{!empty($moveIn['due']) ? $moveIn['due'] : ""}}"
                                                                class="form-control move-in-due @error('moveIn.' . $miKey . '.due') is-invalid @enderror"
                                                        >
                                                        @error('moveIn.' . $miKey . '.due')
                                                        <span style="display: block;" class="invalid-feedback" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-11 mb-3">
                                                        <label for="moveInCostMemo{{ $miKey }}">
                                                            Move-in Cost Memo <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <input
                                                                type="text"
                                                                class="form-control move-in-memo @error('moveIn.' . $miKey . '.memo') is-invalid @enderror"
                                                                name="moveIn[{{ $miKey }}][memo]"
                                                                id="moveInCostMemo{{ $miKey }}"
                                                                value="{{!empty($moveIn['memo']) ? $moveIn['memo'] : ""}}"
                                                        >
                                                        @error('moveIn.' . $miKey . '.memo')
                                                        <span style="display: block;" class="invalid-feedback" role="alert">
                                                            {{ $message }}
                                                        </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-1 mb-3 removeFormRowCell">
                                                        <a href="#">remove <i class="fal fa-times"></i></a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>

                                    <div class="addBillBox mb-3">
                                        <button
                                                id="addMoveInCostsButton"
                                                data-n="1"
                                                data-target="addMoveInCostsBox"
                                                class="addRowButton btn btn-outline-secondary btn-sm"
                                        >
                                            <i class="fal fa-plus-circle mr-1"></i>Add Move-in Cost
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-muted">
                        <button type="submit" name="back" value="back" class="btn btn-cancel btn-sm mr-3 woChecks">
                            <i class="fal fa-arrow-left mr-1"></i>Back
                        </button>
                        <button type="button" name="cancel" class="btn btn-cancel btn-sm mr-3" data-toggle="modal" data-target="#confirmCancelModal">
                            <i class="fal fa-times mr-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm float-right">
                            Continue <i class="fal fa-arrow-right mr-1"></i>
                        </button>
                    </div>
                </div><!-- /propertyForm -->
                @include('leases.add.cancel-modal-partial')
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function() {
            $('.removeFormRowCell').find('a').click(function(e){
                e.preventDefault();
                var formRow = $(this).parent().parent('.form-row')
                formRow.remove();
            });

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
            //$('.yesNoSwitch').find('input').prop("checked", false);

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
                    $(this).attr('disabled',false);
                });
                row.find('label').each(function(){
                    var replfor = $(this).attr('for').replace("0", n);
                    $(this).attr('for',replfor);
                });

                row.find('.removeFormRowCell').find('a').click(function(e){
                    e.preventDefault();
                    var formRow = $(this).parent().parent('.form-row')
                    formRow.remove();
                });

                row.find('.bill-amount').attr('name', 'bill[' + n + '][amount]');
                row.find('.bill-name').attr('name', 'bill[' + n + '][name]');

                row.find('.move-in-amount').attr('name', 'moveIn[' + n + '][amount]');
                row.find('.move-in-due').attr('name', 'moveIn[' + n + '][due]');
                row.find('.move-in-memo').attr('name', 'moveIn[' + n + '][memo]');

                row.find("input[data-type='currency']").on({
                    keyup: function() {
                        formatCurrency(jQuery(this));
                    },
                    blur: function() {
                        formatCurrency(jQuery(this), "blur");
                    }
                });
                row.find("input[data-type='integer']").on({
                    keyup: function() {
                        formatInteger(jQuery(this));
                    }
                });
                row.find("input[data-maxamount]").on({
                    keyup: function() {
                        restrictMaxAmount(jQuery(this));
                    }
                });

                targrtbox.append(row);
            });


            /*var activeCheckboxes = $('.checked-checkbox')
            activeCheckboxes.each(function() {
                    this.checked = true
                    var collapsedContent = $(this).parents('.card-header').first().next('.collapse').first();
                    collapsedContent.collapse('show')
            })*/
        });

    </script>
@endsection
