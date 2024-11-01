<div class="container-fluid">
    <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
        <h1 class="h2 text-center text-sm-left">Create Application</h1>
        <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
            <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>

            <button class="btn btn-primary btn-sm float-sm-right" type="submit" style="color: #fff">
                <i class="fal fa-check-circle mr-1"></i> Save
            </button>
        </div>
    </div>
</div>
<div class="container-fluid">

    {{--@if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session()->get('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session()->get('error') }}
        </div>
    @endif--}}

    <div class="card propertyForm">
        <div class="card-header">
            <i class="fal fa-user-check"></i> General Tenant Information
        </div>

        <div class="card-body bg-light">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">
                        First Name <i class="required fal fa-asterisk"></i>
                    </label>

                    <input
                        type="text"
                        name="firstname"
                        id="validationCustom01"
                        class="form-control @error('firstname') is-invalid @enderror"
                        value="{{ old('firstname') ?? $tenant->name ?? "" }}"
                        required="required">
                    <span class="invalid-feedback" role="alert">
                                    @error('firstname')
                        {{ $message }}
                        @enderror
                                </span>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom02">
                        Last Name <i class="required fal fa-asterisk"></i>
                    </label>

                    <input
                        type="text"
                        name="lastname"
                        id="validationCustom02"
                        class="form-control @error('lastname') is-invalid @enderror"
                        value="{{ old('lastname') ?? $tenant->lastname ?? "" }}"
                        required="required">
                    <span class="invalid-feedback" role="alert">
                                    @error('lastname')
                        {{ $message }}
                        @enderror
                                </span>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="tenantDob">
                        DOB <i class="required fal fa-asterisk"></i>
                    </label>

                    <input
                        type="date"
                        class="form-control @error('dob') is-invalid @enderror"
                        id="tenantDob"
                        name="dob"
                        value="{{ old('dob') ?? \Carbon\Carbon::now()->subYears(18)->format("Y-m-d") }}"
                        max="{{ \Carbon\Carbon::now()->subYears(18)->format("Y-m-d") }}"
                        min="1900-01-01"
                        required="required">
                    <span class="invalid-feedback" role="alert">
                                    @error('dob')
                        {{ $message }}
                        @enderror
                                </span>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="validationCustom03">
                        Email Address <i class="required fal fa-asterisk"></i>
                    </label>

                    <input
                        type="text"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        id="validationCustom03"
                        value="{{ old('email') ?? $tenant->email ?? "" }}"
                        required="required" @if(isset($tenant)) readonly="readonly" @endif>
                    <span class="invalid-feedback" role="alert">
                                    @error('email')
                        {{ $message }}
                        @enderror
                                </span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tenantPhone">
                        Phone <i class="required fal fa-asterisk"></i>
                    </label>

                    <input
                        type="text"
                        data-mask="000-000-0000"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        id="tenantPhone"
                        value="{{ old('phone') ?? $tenant->phone ?? "" }}"
                        required="required">
                    <span class="invalid-feedback" role="alert">
                                    @error('phone')
                        {{ $message }}
                        @enderror
                                </span>
                </div>
            </div>
        </div>

        <div class="card-body pb-1 border-top">

            <div class="card mb-3 applicationSectionExpandForm">
                <div class="card-header bg-light border-0">
                    <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                        <div>Employment & Monthly Income</div>
                    </div>
                </div>

                <div class="card-body bg-light border-top">

                    @foreach (old('employmentAndlIncomes') ?? [] as $key => $value)
                        <div class="addRowBox">
                            <div class="form-row">
                                <div class="col-md-8 mb-3">
                                    <label for="addEmpName0">Employment</label>

                                    <input
                                        type="text"
                                        class="form-control @error('employmentAndlIncomes.' . $key . '.employment') is-invalid @enderror"
                                        name="employmentAndlIncomes[{{$key}}][employment]"
                                        value="{{ old('employmentAndlIncomes.' . $key . '.employment') }}"
                                    >

                                    @error('employmentAndlIncomes.' . $key . '.employment')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="addEmpAmount0">
                                        Income <i class="required fal fa-asterisk"></i>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>

                                        <input
                                            type="text"
                                            class="form-control @error('employmentAndlIncomes.' . $key . '.income') is-invalid @enderror"
                                            name="employmentAndlIncomes[{{$key}}][income]"
                                            value="{{ old('employmentAndlIncomes.' . $key . '.income') }}"
                                            data-type="currency"
                                            maxlength="12"
                                            data-maxamount="9999999"
                                        >
                                        <span class="invalid-feedback" role="alert">
                                                        @error('employmentAndlIncomes.' . $key . '.income')
                                            {{ $message }}
                                            @enderror
                                                    </span>
                                    </div>
                                </div>

                                <div class="col-md-1 mb-3 removeFormRowCell">
                                    <a href="#">remove <i class="fal fa-times"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div id="addEmpItemsBox" class="addRowBox">
                        <div class="form-row rowTemplate">
                            <div class="col-md-8 mb-3">
                                <label for="addEmpName0">Employment</label>
                                <input type="text" class="form-control" name="employmentAndlIncomes[1][employment]" disabled required>
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="employmentAndlIncomes.1.employment"></strong>
                                            </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="addEmpAmount0">
                                    Income <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="input-group">

                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>

                                    <input
                                        type="text"
                                        data-type="currency"
                                        maxlength="12"
                                        data-maxamount="9999999"
                                        class="form-control"
                                        name="employmentAndlIncomes[1][income]"
                                        required="required"
                                        disabled >

                                    <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="employmentAndlIncomes.1.income"></strong>
                                                </span>
                                </div>
                            </div>
                            <div class="col-md-1 mb-3 removeFormRowCell">
                                <a href="#">remove <i class="fal fa-times"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="addBillBox mb-3">
                        <button
                            id="addEmpButton"
                            data-n="{{ max(array_merge(array_keys(old('employmentAndlIncomes') ?? []), [0] )) + 1 }}"
                            data-target="addEmpItemsBox"
                            class="addRowButton btn btn-outline-secondary btn-sm"
                        >
                            <i class="fal fa-plus-circle mr-1"></i> add employment
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-3 applicationSectionExpandForm">
                <div class="card-header bg-light border-0">
                    <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                        <div>Additional Monthly Income</div>
                    </div>
                </div>

                <div class="card-body bg-light border-top">
                    @foreach (old('incomes') ?? [] as $key => $value)
                        <div class="addRowBox">
                            <div class="form-row">
                                <div class="col-md-8 mb-3">
                                    <label for="addIncomeName0">
                                        Description <i class="required fal fa-asterisk"></i>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control @error('incomes.' . $key . '.description') is-invalid @enderror"
                                        name="incomes[{{ $key }}][description]"
                                        value="{{ old('incomes.' . $key . '.description') }}"
                                        required="required">
                                    <span class="invalid-feedback" role="alert">
                                                @error('incomes.' . $key . '.description')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="addIncomeAmount0">
                                        Amount <i class="required fal fa-asterisk"></i>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>

                                        <input
                                            type="number"
                                            class="form-control number__check @error('incomes.' . $key . '.amount') is-invalid @enderror"
                                            name="incomes[{{ $key }}][amount]"
                                            value="{{ old('incomes.' . $key . '.amount') }}"
                                            data-type="currency"
                                            maxlength="12"
                                            data-maxamount="9999999"
                                            required="required">
                                        <span class="invalid-feedback" role="alert">
                                                    @error('incomes.' . $key . '.amount')
                                            {{ $message }}
                                            @enderror
                                                </span>
                                    </div>
                                </div>

                                <div class="col-md-1 mb-3 removeFormRowCell">
                                    <a href="#">remove <i class="fal fa-times"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach


                    <div id="addIncomeItemsBox" class="addRowBox">
                        <div class="form-row rowTemplate">
                            <div class="col-md-8 mb-3">
                                <label for="addIncomeName0">
                                    Description <i class="required fal fa-asterisk"></i>
                                </label>
                                <input type="text" class="form-control" name="incomes[1][description]" required="required" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="incomes.1.description"></strong>
                                            </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="addIncomeAmount0">
                                    Amount <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>

                                    <input
                                        type="text"
                                        data-type="currency"
                                        class="form-control"
                                        name="incomes[1][amount]"
                                        maxlength="12"
                                        data-maxamount="9999999"
                                        required="required"
                                        disabled >

                                    <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="incomes.1.amount"></strong>
                                                </span>
                                </div>
                            </div>

                            <div class="col-md-1 mb-3 removeFormRowCell">
                                <a href="#">remove <i class="fal fa-times"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="addBillBox mb-3">
                        <button
                            id="addIncomeButton"
                            data-n="{{ max(array_merge(array_keys(old('incomes') ?? []), [0] )) + 1 }}"
                            data-target="addIncomeItemsBox"
                            class="addRowButton btn btn-outline-secondary btn-sm"
                        >
                            <i class="fal fa-plus-circle mr-1"></i> add income
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-3 applicationSectionExpandForm">
                <div class="card-header bg-light border-0">
                    <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                        <div>Residence history</div>
                    </div>
                </div>

                <div class="card-body bg-light border-top">
                    @foreach (old('residenceHistories') ?? [] as $key => $value)
                        <div class="addRowBox">
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label for="residenceDate0">
                                        Date <i class="required fal fa-asterisk"></i>
                                    </label>

                                    <input
                                        type="date"
                                        name="residenceHistories[{{$key}}][start_date]"
                                        class="form-control @error('residenceHistories.' . $key . '.start_date') is-invalid @enderror"
                                        id="residenceDate0"
                                        value="{{ old('residenceHistories.' . $key . '.start_date') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('residenceHistories.' . $key . '.start_date')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="residenceEndDate0">End Date</label>

                                    <input
                                        type="date"
                                        name="residenceHistories[{{$key}}][end_date]"
                                        class="form-control @error('residenceHistories.' . $key . '.end_date') is-invalid @enderror"
                                        id="residenceEndDate0"
                                        value="{{ old('residenceHistories.' . $key . '.end_date') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('residenceHistories.' . $key . '.end_date')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-checkbox pt-2 ml-2">
                                        <input
                                            type="checkbox"
                                            name="residenceHistories[{{$key}}][current]"
                                            class="custom-control-input endDateCurrent @error('residenceHistories.' . $key . '.current') is-invalid @enderror"
                                            id="endDateCurrent1"
                                            value="1"
                                        >
                                        <span class="invalid-feedback" role="alert">
                                                    @error('residenceHistories.' . $key . '.current')
                                            {{ $message }}
                                            @enderror
                                                </span>

                                        <label class="custom-control-label" for="endDateCurrent0">Current</label>
                                    </div>
                                </div>

                                <div class="clear"></div>

                                <div class="col-md-6 mb-3">
                                    <label for="residenceAddress0">
                                        Address
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control @error('residenceHistories.' . $key . '.address') is-invalid @enderror"
                                        name="residenceHistories[{{$key}}][address]"
                                        id="residenceAddress0"
                                        value="{{ old('residenceHistories.' . $key . '.address') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('residenceHistories.' . $key . '.address')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="residenceCity0">
                                        City
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control @error('residenceHistories.' . $key . '.city') is-invalid @enderror"
                                        name="residenceHistories[{{$key}}][city]"
                                        id="residenceCity0"
                                        value="{{ old('residenceHistories.' . $key . '.city') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('residenceHistories.' . $key . '.city')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="residenceState0">
                                        State
                                    </label>

                                    <select
                                        name="residenceHistories[{{$key}}][state_id]"
                                        class="custom-select fixedMaxInputWidth @error('residenceHistories.' . $key . '.state_id') is-invalid @enderror"
                                        id="residenceState0"
                                    >
                                        <option value hidden>Select</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}" {{ old('residenceHistories.' . $key . '.state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" role="alert">
                                                @error('residenceHistories.' . $key . '.state_id')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-1 mb-3 removeFormRowCell">
                                    <a href="#">remove <i class="fal fa-times"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div id="addResidenceItemsBox" class="addRowBox">
                        <div class="form-row rowTemplate">
                            <div class="col-md-3 mb-3">
                                <label for="residenceDate0">Date</label>
                                <input type="date" value="null" name="residenceHistories[1][start_date]" class="form-control" placeholder="" id="residenceDate0" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="residenceHistories.1.start_date"></strong>
                                            </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="residenceEndDate0">End Date</label>
                                <input type="date" value="null" name="residenceHistories[1][end_date]" class="form-control" placeholder="" id="residenceEndDate0" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="residenceHistories.1.end_date"></strong>
                                            </span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-checkbox pt-2 ml-2">
                                    <input type="checkbox" name="residenceHistories[1][current]" class="custom-control-input endDateCurrent" id="endDateCurrent1" value="1" disabled >
                                    <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="residenceHistories.1.current"></strong>
                                                </span>
                                    <label class="custom-control-label" for="endDateCurrent0">Current</label>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="col-md-6 mb-3">
                                <label for="residenceAddress0">Address</label>
                                <input type="text" class="form-control" name="residenceHistories[1][address]" id="residenceAddress0" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="residenceHistories.1.address"></strong>
                                                </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="residenceCity0">City</label>
                                <input type="text" class="form-control" name="residenceHistories[1][city]" id="residenceCity0" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="residenceHistories.1.city"></strong>
                                                </span>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="residenceState0">State</label>
                                <select name="residenceHistories[1][state_id]" class="custom-select fixedMaxInputWidth" id="residenceState0" required disabled >
                                    <option value hidden>Select</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="residenceHistories.1.state_id"></strong>
                                            </span>
                            </div>

                            <div class="col-md-1 mb-3 removeFormRowCell">
                                <a href="#">remove <i class="fal fa-times"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="addBillBox mb-3">
                        <button
                            id="addResidenceButton"
                            data-n="{{ max(array_merge(array_keys(old('residenceHistories') ?? []), [0] )) + 1 }}"
                            data-target="addResidenceItemsBox"
                            class="addRowButton btn btn-outline-secondary btn-sm"
                        >
                            <i class="fal fa-plus-circle mr-1"></i> add address
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-3 applicationSectionExpandForm">
                <div class="card-header bg-light border-0">
                    <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                        <div>References</div>
                    </div>
                </div>

                <div class="card-body bg-light border-top">
                    @foreach (old('references') ?? [] as $key => $value)
                        <div class="addRowBox">
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="referenceName">
                                        Name
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control @error('references.' . $key . '.name') is-invalid @enderror"
                                        name="references[{{$key}}][name]"
                                        value="{{ old('references.' . $key . '.name') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('references.' . $key . '.name')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="referenceEmail">
                                        Email
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control @error('references.' . $key . '.email') is-invalid @enderror"
                                        name="references[{{$key}}][email]"
                                        value="{{ old('references.' . $key . '.email') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('references.' . $key . '.email')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="referencePhone">
                                        Phone
                                    </label>

                                    <input
                                        type="text"
                                        data-mask="000-000-0000"
                                        class="form-control @error('references.' . $key . '.phone') is-invalid @enderror"
                                        name="references[{{$key}}][phone]"
                                        data-type="phone"
                                        value="{{ old('references.' . $key . '.phone') }}"
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('references.' . $key . '.phone')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>

                                <div class="col-md-1 mb-3 removeFormRowCell">
                                    <a href="#">remove <i class="fal fa-times"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div id="addRefItemsBox" class="addRowBox">
                        <div class="form-row rowTemplate">
                            <div class="col-md-4 mb-3">
                                <label for="referenceName">Name</label>
                                <input type="text" class="form-control" name="references[1][name]" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="references.1.name"></strong>
                                            </span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="referenceEmail">Email</label>
                                <input type="text" class="form-control" name="references[1][email]" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="references.1.email"></strong>
                                            </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="referencePhone">Phone</label>
                                <input type="text" data-mask="000-000-0000" class="form-control" name="references[1][phone]" data-type="phone" disabled >
                                <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                <strong name="references.1.phone"></strong>
                                            </span>
                            </div>
                            <div class="col-md-1 mb-3 removeFormRowCell">
                                <a href="#">remove <i class="fal fa-times"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="addBillBox mb-3">
                        <button
                            id="addRefButton"
                            data-n="{{ max(array_merge(array_keys(old('references') ?? []), [0] )) + 1 }}"
                            data-target="addRefItemsBox"
                            class="addRowButton btn btn-outline-secondary btn-sm"
                        >
                            <i class="fal fa-plus-circle mr-1"></i> add reference
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-3 applicationSectionExpandForm">
                <div class="card-header bg-light border-0">
                    <div class="filterToolbar btn-toolbar d-flex justify-content-between">
                        <div>Pets</div>
                    </div>
                </div>

                <div>
                    <div class="card-body bg-light border-top">
                        @foreach (old('pets') ?? [] as $key => $value)
                            <div class="addRowBox">
                                <div class="form-row">
                                    <div class="col-md-5 mb-3">
                                        <label for="petType0">
                                            Type <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <select
                                            name="pets[{{$key}}][pets_type_id]"
                                            class="custom-select fixedMaxInputWidth @error('pets.' . $key . '.pets_type_id') is-invalid @enderror"
                                            id="petType0"
                                        >
                                            @foreach($petsTypes as $petsType)
                                                <option value="{{ $petsType->id }}" {{ old('pets.' . $key . '.pets_type_id') == $petsType->id ? 'selected' : ''}}>
                                                    {{ $petsType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" role="alert">
                                                    @error('pets.' . $key . '.pets_type_id')
                                            {{ $message }}
                                            @enderror
                                                </span>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="petType0">
                                            Description <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control @error('pets.' . $key . '.description') is-invalid @enderror"
                                            name="pets[{{$key}}][description]"
                                            id="petType0"
                                            value="{{ old('pets.' . $key . '.description') }}"
                                        >
                                        <span class="invalid-feedback" role="alert">
                                                    @error('pets.' . $key . '.description')
                                            {{ $message }}
                                            @enderror
                                                </span>
                                    </div>

                                    <div class="col-md-1 mb-3 removeFormRowCell">
                                        <a href="#">remove <i class="fal fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div id="addPetItemsBox" class="addRowBox">
                            <div class="form-row rowTemplate">
                                <div class="col-md-5 mb-3">
                                    <label for="petType0">Type <i class="required fal fa-asterisk"></i></label>
                                    <select name="pets[1][pets_type_id]" class="custom-select fixedMaxInputWidth" id="petType0" disabled>
                                        @foreach($petsTypes as $petsType)
                                            <option value="{{ $petsType->id }}">{{ $petsType->name }}</option>
                                        @endforeach
                                    </select>
                                    <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="pets.1.pets_type_id"></strong>
                                                </span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="petType0">Description <i class="required fal fa-asterisk"></i></label>
                                    <input type="text" class="form-control" name="pets[1][description]" id="petType0" disabled>
                                    <span style="display: none;" id="modal-text-error" class="invalid-feedback" role="alert">
                                                    <strong name="pets.1.description"></strong>
                                                </span>
                                </div>
                                <div class="col-md-1 mb-3 removeFormRowCell">
                                    <a href="#">remove <i class="fal fa-times"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="addBillBox mb-3">
                            <button
                                id="addPetButton"
                                data-n="{{ max(array_merge(array_keys(old('pets') ?? []), [0] )) + 1 }}"
                                data-target="addPetItemsBox"
                                class="addRowButton btn btn-outline-secondary btn-sm"
                            >
                                <i class="fal fa-plus-circle mr-1"></i>add pet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-header border-top">
            <i class="fal fa-shield-alt"></i> Additional Information
        </div>
        <div class="card-body bg-light">
            <div class="custom-control custom-checkbox pt-2 ml-2">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    name="smoke"
                    id="tenantCheck01"
                    value="1"
                    {{ old('smoke') ? 'checked' : '' }}
                >

                <label class="custom-control-label" for="tenantCheck01">Do you smoke?</label>
            </div>

            <div class="custom-control custom-checkbox pt-2 ml-2">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    name="evicted_or_unlawful"
                    id="tenantCheck02"
                    value="1"
                    {{ old('evicted_or_unlawful') ? 'checked' : '' }}
                >

                <label class="custom-control-label" for="tenantCheck02">Have you ever been evicted from a rental or had an unlawful detainer judgement against you?</label>
            </div>

            <div class="custom-control custom-checkbox pt-2 ml-2">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    name="felony_or_misdemeanor"
                    id="tenantCheck03"
                    value="1"
                    {{ old('felony_or_misdemeanor') ? 'checked' : '' }}
                >

                <label class="custom-control-label" for="tenantCheck03">Have you ever been convicted of a felony or misdemeanor (other than a traffic or parking violation)?</label>
            </div>

            <div class="custom-control custom-checkbox pt-2 ml-2">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    name="refuse_to_pay_rent"
                    id="tenantCheck04"
                    value="1"
                    {{ old('refuse_to_pay_rent') ? 'checked' : '' }}
                >

                <label class="custom-control-label" for="tenantCheck04">Have you ever refused to pay rent when it was due?</label>
            </div>
        </div>

        @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
            <div class="card-header border-top greyCardHead">
                <i class="fal fa-building"></i> Property and Rent Information
            </div>

            <div class="card-body bg-light">
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        @php
                            if(!empty(Request::get('unit_id'))) {
                                $currUnit = \App\Models\Unit::find((int)Request::get('unit_id'));
                                $currProperty = !empty($currUnit) ? $currUnit->property : null;
                            }
                        @endphp
                        <label for="validationCustom04">Property <i class="required fal fa-asterisk"></i></label>

                        <select name="property_id" class="custom-select property-select" id="validationCustom04" required="required">
                            <option hidden value>Choose a property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}"
                                    {{ !empty($currProperty) && empty(old('property_id')) && $currProperty->id == $property->id  ? "selected" : ""}}
                                    {{ !empty(old('property_id')) && old('property_id') == $property->id  ? "selected" : ""}}

                                >
                                    {{ !empty($property->type) ? $property->type->name : "" }} - {{ $property->full_address }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" role="alert">
                                        @error('property_id')
                            {{ $message }}
                            @enderror
                                    </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="validationCustom05">Unit <i class="required fal fa-asterisk"></i></label>

                        <select name="unit_id" class="custom-select inut-select" id="validationCustom05" required="required" >
                            <option hidden value="">Choose a unit</option>
                            @if (!empty($currProperty))
                                @foreach($currProperty->units as $unit)
                                    <option value="{{ $unit->id }}" {{ $currUnit->id == $unit->id  ? "selected" : ""}}>{{ $unit->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="invalid-feedback" role="alert">
                                        @error('unit_id')
                            {{ $message }}
                            @enderror
                                    </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="startDate">Start Date <i class="required fal fa-asterisk"></i></label>

                        <input
                            type="date"
                            name="start_date"
                            class="form-control @error('start_date') is-invalid @enderror"
                            id="startDate"
                            value="{{ old('start_date') }}"
                            required="required">
                        <span class="invalid-feedback" role="alert">
                                        @error('start_date')
                            {{ $message }}
                            @enderror
                                    </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="endDate">End Date</label>

                        <input
                            type="date"
                            class="form-control @error('end_date') is-invalid @enderror"
                            name="end_date"
                            id="endDate"
                            value="{{ old('end_date') }}">
                        <span class="invalid-feedback" role="alert">
                                        @error('end_date')
                            {{ $message }}
                            @enderror
                                    </span>
                    </div>
                </div>
            </div>
        @else
            @if(!empty(Request::get('unit_id')))
                <div class="card-header border-top greyCardHead">
                    <i class="fal fa-building"></i> Property Information
                </div>

                <div class="card-body bg-light">
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            @php
                                if(!empty(Request::get('unit_id'))) {
                                    $currUnit = \App\Models\Unit::find((int)Request::get('unit_id'));
                                    $currProperty = !empty($currUnit) ? $currUnit->property : null;
                                }
                            @endphp
                            <label for="property_id_tenant">Property <i class="required fal fa-asterisk"></i></label>
                            <select name="property_id_tenant" class="custom-select property-select" id="property_id_tenant" readonly="readonly">
                                <option value="{{ $currProperty->id }}" selected>
                                    {{ !empty($currProperty->type) ? $currProperty->type->name : "" }} - {{ $currProperty->full_address }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validationCustom05">Unit <i class="required fal fa-asterisk"></i></label>
                            <select name="unit_id" class="custom-select inut-select" id="validationCustom05" readonly="readonly" >
                                <option value="{{ $currUnit->id }}" selected>{{ $currUnit->name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        @if(Auth::user()->isLandlord() || Auth::user()->isPropManager())
            <div class="card-header border-top greyCardHead">
                <i class="fal fa-clipboard"></i> Internal Notes
            </div>
            <div class="card-body bg-light">
                <div class="inRowComment">
                    <i class="fal fa-info-circle"></i> Internal Notes are not visible to the tenant
                </div>
                <textarea title="Internal Notes" class="form-control" id="internalNotesField" name="internal_notes" maxlength="4000">{{ old('internal_notes') }}</textarea>
            </div>
        @else
            <div class="card-header border-top greyCardHead">
                <i class="fal fa-clipboard"></i> Notes
            </div>
            <div class="card-body bg-light">
                <div class="inRowComment">
                    <i class="fal fa-info-circle"></i> Notes are visible for the landlord and property manager
                </div>
                <textarea title="Notes" class="form-control" id="notesField" name="notes" maxlength="4000">{{ old('notes') }}</textarea>
            </div>
        @endif

        <div class="card-header border-top greyCardHead">
            <i class="fal fa-file-alt"></i> Supportive Documents
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
                            Upload here the attachments that you want to be visible to your tenants.<br>
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

        <div class="card-footer text-muted">
            <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3">
                <i class="fal fa-times mr-1"></i> Cancel
            </a>

            <button
                type="submit"
                role="submit"
                class="btn btn-primary btn-sm float-right"
            >
                <i class="fal fa-check-circle mr-1"></i> Create Application
            </button>
        </div>
    </div><!-- /propertyForm -->
</div>
</div>
