@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <span>Lease</span>
        >
        <span>Create Lease</span>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div
                class="d-block d-md-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <h1 class="h2 text-center text-sm-left">Create Lease</h1>
                <ul id="progressbar">
                    <li class="active progress1">
                        <div>Tenant and Property</div>
                        <div>Information</div>
                    </li>
                    <li class="progress2">
                        <div>Extra Fees and</div>
                        <div>Assistance</div>
                    </li>
                    <li class="progress3">
                        <div>Payments and</div>
                        <div>Documents</div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container-fluid">
            <form class="needs-validation checkUnloadByDefoult" novalidate method="POST"
                  action="{{ route('leases/add') }}">
                @csrf

                <input type="hidden" name="application_id" value="{{ $application ? $application->id : '' }}">
                <input type="hidden" name="step" id="step" value="{{ $step }}">

                <div class="card propertyForm">
                    <div class="card-header">
                        Your Tenant Information
                    </div>

                    <div class="card-body bg-light">
                        <div class="form-row">
                            <div class="col-md mb-3">
                                <label for="firstNameField">
                                    First Name <i class="required fal fa-asterisk"></i>
                                </label>

                                <input type="text" name="firstname" id="firstNameField" required="required"
                                       maxlength="127"
                                       class="form-control @error('firstname') is-invalid @enderror"
                                       value="{{ old('firstname') ?? $data['firstname'] ?? ($application ? $application->firstname : '') }}">
                                <span class="invalid-feedback" role="alert">
                                    @error('firstname')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md mb-3">
                                <label for="lastNameField">
                                    Last Name <i class="required fal fa-asterisk"></i>
                                </label>

                                <input type="text" name="lastname" id="lastNameField" required="required"
                                       maxlength="127"
                                       class="form-control @error('lastname') is-invalid @enderror"
                                       value="{{ old('lastname') ?? $data['lastname'] ?? ($application ? $application->lastname : '') }}">
                                <span class="invalid-feedback" role="alert">
                                    @error('lastname')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="w-100 d-block d-lg-none"></div>

                            <div class="col-md @if(!empty($application)) mb-3 @endif">
                                <label for="emailField">
                                    Email Address <i class="required fal fa-asterisk"></i>
                                </label>

                                <input type="text" name="email" id="emailField" required="required" maxlength="127"
                                       {{ $application ? ' readonly="readonly"' : '' }}
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') ?? $data['email'] ?? ($application ? $application->email : '') }}">
                                <span class="invalid-feedback" role="alert">
                                    @error('email')
                                    {{ $message }}
                                    @enderror
                                </span>
                                @if(empty($application))
                                    <div class="inRowComment mt-2 pb-0">
                                        <i class="fal fa-info-circle"></i> By entering tenant’s email address, our
                                        system will send an automatic invitation to the given tenant to join our system.
                                        MYNESTHUB is free to join for tenants!
                                    </div>
                                @endif
                            </div>

                            <div class="col-md mb-3">
                                <label for="phoneField">
                                    Phone <i class="required fal fa-asterisk"></i>
                                </label>

                                <input type="text" name="phone" id="phoneField" required="required" maxlength="20"
                                       data-mask="000-000-0000"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('phone') ?? $data['phone'] ?? ($application ? $application->phone : '') }}">
                                <span class="invalid-feedback" role="alert">
                                    @error('phone')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-header border-top">
                        Property you are setting up rent for
                    </div>

                    <div class="card-body bg-light">
                        @if(Auth::user()->vacantUnitsCount() == 0)
                            <div class="alert alert-warning" role="alert">
                                All of your units are currently occupied. Go to your properties units, then click on
                                specific unit lease and press 'End Lease' to make unit vacant.
                            </div>
                        @endif

                        @php
                            $selectedUnitId = old('unit') ?? $data['unit_id'] ?? (!empty($unit) ? $unit->id : '');
                            $selectedPropertyId = old('property') ?? $data['property'] ?? ( !empty($unit) ? $unit->property->id : null) ?? $property_id ?? '';
                            if(($selectedPropertyId != '') && (App\Models\Property::find($selectedPropertyId)->status() == 0)){
                                $selectedPropertyId = '';
                            }
                            $selectedUnitList = $selectedPropertyId ? App\Models\Property::find($selectedPropertyId)->units->sortBy('name') : [];

                            $properties = Auth::user()->properties->sortBy('address');
                        @endphp
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="propertyField">
                                    Property <i class="required fal fa-asterisk"></i>
                                </label>

                                <select name="property" id="propertyField" required="required"
                                        class="custom-select @error('property') is-invalid @enderror">
                                    <option value="" hidden disabled @if($selectedPropertyId == "") selected @endif>
                                        Choose a property
                                    </option>
                                    @foreach ($properties as $property)
                                        @if($property->status() != 0)
                                            <option value="{{ $property->id}}"
                                                    @if($selectedPropertyId == $property->id) selected @endif>
                                                {{ $property->address }}
                                            </option>
                                        @endif
                                    @endforeach
                                    @foreach ($properties as $property)
                                        @if($property->status() == 0)
                                            <option value="{{ $property->id}}" disabled="disabled">
                                                {{ $property->address }} (occupied)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" role="alert">
                                    @error('property')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="unitField">
                                    Unit <i class="required fal fa-asterisk"></i>
                                </label>

                                <select name="unit" id="unitField" required="required"
                                        @if(empty($selectedUnitList)) disabled @endif
                                        class="custom-select @error('unit') is-invalid @enderror">
                                    @if($selectedPropertyId)
                                        <option hidden value="">Select Unit</option>
                                    @else
                                        <option hidden value="">Choose a property first</option>
                                    @endif
                                    @foreach ($selectedUnitList as $selectedUnit)
                                        @if($selectedUnit->status != 0)
                                            <option value="{{ $selectedUnit->id}}"
                                                    @if($selectedUnit->id == $selectedUnitId) selected @endif >
                                                {{ $selectedUnit->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                    @foreach ($selectedUnitList as $selectedUnit)
                                        @if($selectedUnit->status == 0)
                                            <option value="{{ $selectedUnit->id}}" disabled>
                                                {{ $selectedUnit->name }} (occupied)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" role="alert">
                                    @error('unit')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-header border-top">
                        Lease Details
                    </div>

                    <div class="card-body bg-light">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="startDate">
                                    Start Date <i class="required fal fa-asterisk"></i>
                                </label>

                                <input
                                    type="date"
                                    value="{{
                                        old('start_date')
                                        ?? ($data && $data['start_date'] ? Carbon\Carbon::parse($data['start_date'])->format("Y-m-d") : null)
                                        ?? ($application && $application->start_date ? Carbon\Carbon::parse($application->start_date)->format("Y-m-d") : null)
                                        ?? Carbon\Carbon::now()->format("Y-m-d")
                                    }}"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    id="startDate" required="required"
                                    name="start_date"
                                >
                                <span class="invalid-feedback" role="alert">
                                    @error('start_date')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="endDate">End Date</label>
                                <input
                                    type="date"
                                    value="{{
                                        old('end_date')
                                        ?? ($data && $data['end_date'] ? Carbon\Carbon::parse($data['end_date'])->format("Y-m-d") : null)
                                        ?? ($application && $application->end_date ? Carbon\Carbon::parse($application->end_date)->format("Y-m-d") : null)
                                        ?? Carbon\Carbon::now()->addYear()->format("Y-m-d")
                                    }}"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    id="endDate"
                                    name="end_date"
                                    {{ (
                                            ( !(old('start_date') || ($data && $data['start_date'])) && ($application && !$application->end_date)) ||
                                            (  (old('start_date') && old('month_to_month')) || ($data && $data['start_date'] && $data['month_to_month']))
                                        ) ?  'disabled' : ''}}
                                >
                                <span class="invalid-feedback" role="alert">
                                    @error('end_date')
                                    {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-checkbox pt-2 ml-2">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input @error('month_to_month') is-invalid @enderror"
                                        id="monthToMonth"
                                        name="month_to_month"
                                        {{ (
                                                ( !(old('start_date') || ($data && $data['start_date'])) && ($application && !$application->end_date)) ||
                                                (  (old('start_date') && old('month_to_month')) || ($data && $data['start_date'] && $data['month_to_month']))
                                            ) ? 'checked' : ''}}
                                    >
                                    <label
                                        class="custom-control-label"
                                        for="monthToMonth"
                                    >
                                        Month to Month (no end date)
                                    </label>
                                    <span class="invalid-feedback" role="alert">
                                        @error('month_to_month')
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="due_date">Monthly Due Date <i class="required fal fa-asterisk"></i></label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">day</span>
                                    </div>

                                    <select name="due_date" id="due_date"
                                            class="form-control @error('due_date') is-invalid @enderror">
                                        @for($day = 1; $day <= 31; $day++)
                                            <option
                                                value="{{ $day }}" {{ (old('due_date') && (old('due_date') == $day)) || (!old('due_date') && $data && $data['monthly_due_date'] && ($data['monthly_due_date'] == $day)) ? 'selected' : "" }}>{{ $day }}</option>
                                        @endfor
                                    </select>
                                    <div class="input-group-append">
                                        <span class="input-group-text">of the month</span>
                                    </div>
                                    @error('due_date')
                                    <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="monthlyRentAmountField">
                                    Monthly rent amount <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>

                                    <input name="amount" id="monthlyRentAmountField" type="text" required="required"
                                           data-type="currency"
                                           data-maxamount="9999999"
                                           maxlength="20"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           value="{{ old('amount') ?? $data['amount'] ?? '' }}">
                                    <span class="invalid-feedback" role="alert">
                                        @error('amount')
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <button type="button" name="cancel" class="btn btn-cancel btn-sm mr-3" data-toggle="modal"
                                data-target="#confirmCancelModal">
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
        $(document).ready(
            function () {
                $('#propertyField').change(function (e) {
                    $(".preloader").fadeIn("fast");
                    $('#unitField').removeAttr('disabled');
                    var property_id = $(this).val();
                    $("#unitField").load(
                        '{{ route('leases/ajax-get-property-unit') }}',
                        {
                            '_token': '{{ csrf_token() }}',
                            'property_id': property_id
                        },
                        function () {
                            $(".preloader").fadeOut("fast");
                        }
                    );
                });
                if ('{{ $selectedPropertyId }}' == '') {
                    $('#propertyField').val('');
                    $('#unitField').attr('disabled', 'disabled');
                }
            }
        );
    </script>
    <script>
        $(document).ready(function () {
            $('#monthToMonth').change(function () {
                $('#endDate').val('');
                $('#endDate').prop('disabled', this.checked);
            });
            $('.yesNoSwitch').find('input').change(function () {
                var collapsedContent = $(this).parents('.card-header').first().next('.collapse').first();
                if (this.checked) {
                    collapsedContent.collapse('show')
                } else {
                    collapsedContent.collapse('hide')
                }
            });
            $('.yesNoSwitch').find('input').prop("checked", false);

            $('.addRowButton').click(function (e) {
                e.preventDefault();
                var n = $(this).data('n');
                n++;
                $(this).data('n', n);
                var target = $(this).data('target');

                var targrtbox = $('#' + target);
                var row = targrtbox.find('.rowTemplate').clone();
                row.removeClass('rowTemplate');
                row.find('input').each(function () {
                    var replid = $(this).attr('id').replace("0", n);
                    $(this).attr('id', replid);
                });
                row.find('label').each(function () {
                    var replfor = $(this).attr('for').replace("0", n);
                    $(this).attr('for', replfor);
                });
                row.find('.removeFormRowCell').find('a').click(function (e) {
                    e.preventDefault();
                    $(this).parent().parent('.form-row').remove();
                });
                targrtbox.append(row);
            });

        });
    </script>
@endsection
