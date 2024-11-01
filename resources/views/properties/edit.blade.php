@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ url('properties') }}">
            Properties
        </a>
        >
        <a href="{{route('properties/edit', ['id' => $property->id])}}">
            {{$property->address}}
        </a>
    </div>

    <div class="container-fluid pb-4">
        @include('properties.edit-address-partial')

        <div class="container-fluid">
            <div class="row">

                <div class="col-md-9 order-md-last mb-4 mb-md-0">

                    <form class="needs-validation checkUnload" novalidate method="POST" enctype="multipart/form-data">
                        @csrf

                            <ul class="nav nav-tabs propertyTabs">
                                <li class="nav-item mobileActive">
                                    <span class="nav-link active" data-href="{{ route('properties/edit', ['property' => $property->id]) }}">Unit Information</span>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('properties/expenses', ['property' => $property->id]) }}">Expenses & Profit</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('properties/documents', ['property' => $property->id]) }}">Documents</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('properties/operations', ['property' => $property->id]) }}">Advanced</a>
                                </li>
                            </ul>

                        <!--<style>
                            @media (max-width: 768px) {
                                .propertyTabs{
                                    display: flex;
                                    flex-direction: column;
                                    padding-bottom: 10px;
                                }
                                .propertyTabs .nav-item span.active{
                                    border: none;
                                    font-size: 2em;
                                    line-height: 1.2;
                                    padding-left: 0;
                                    padding-right: 0;
                                    margin-bottom: 8px;
                                    text-align: center;
                                }
                                .propertyTabs .nav-item a{
                                    background-color: #c9deff !important;
                                    margin-bottom: 8px;
                                    border-radius: 0.5em;
                                    text-align: center;
                                }
                                .propertyTabs .nav-item{
                                    order: 2;
                                }
                                .propertyTabs .nav-item.mobileActive{
                                    order: 1;
                                }

                            }

                            </style>-->

                            <div class="card propertyForm">
                            {{--<div class="card-header d-flex justify-content-between withButton">
                                General Information
                                <button type="button" style="margin: -5px 0;" class="btn btn-light btn-sm text-muted" data-toggle="modal" data-target="#editDetailsModal">Edit <i class="fas fa-pencil-alt ml-1"></i></button>
                            </div>
                            <div class="card-body bg-light">

                                <div class="generalEditableBlock">
                                    <div class="editableBox d-flex align-middle" data-toggle="modal" data-target="#editDetailsModal">
                                        <span class="h2 text-primary2 mr-3 mb-0">{!! $property->icon() !!}</span>
                                        <span class="editThis h4 mb-0 pt-1">
                                            <span class="editableData"
                                                  id="firstnameLastnameView"
                                                  data-pk="firstnameLastname"
                                                  data-type="text"
                                                  data-label="Tenant Name"
                                            >
                                                {{ $property->address }},
                                                {{ $property->city }},
                                                {{ $property->state->code }},
                                                {{ $property->zip }}</span><i class="fas fa-pencil-alt"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            --}}

                                <div class="card-body" id="newFullData">

                                    @if($showArchiveButton || !empty(Request::get('archived_units')))
                                        <div class="filterToolbar btn-toolbar pb-3 justify-content-between">
                                            @if (empty(Request::get('archived_units')))
                                                {{--}}
                                                <button
                                                        onclick="window.location='{{ route('properties/edit', ['property' => $property->id, "archived_units" => 1]) }}'"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="View Archived Units"
                                                        class="btn btn-sm btn-light mr-1 d-inline-block"
                                                        type="button"
                                                >
                                                    <i class="fal fa-file-archive mr-1"></i>
                                                </button>
                                                {{--}}
                                                <span></span>
                                                <a href="{{ route('properties/edit', ['property' => $property->id, "archived_units" => 1]) }}" class="btn btn-outline-secondary btn-sm"><i class="fal fa-file-archive mr-1"></i> View Archived Units</a>
                                            @else
                                                <h4 class="m-0 p-0">Archived Units</h4>
                                                <a href="{{ route('properties/edit', ['property' => $property->id]) }}" class="btn btn-cancel btn-sm"><i class="fal fa-times mr-1"></i> Exit Archive</a>
                                            @endif
                                        </div>
                                    @endif

                                    @if (count($units) === 0)
                                        <div class="mb-3">
                                            <p class="alert alert-info">
                                                No units found.
                                            </p>
                                        </div>
                                    @endif

                                    @foreach ($units as $key => $unit)
                                    <div class="card unitCard mb-3" id="newUnit{{$unit->id}}">
                                        <div class="d-block d-sm-table propCardTable">
                                            @if($unit->archived)
                                                <div class="d-block d-sm-table-row">
                                                    <span
                                                       class="cardImgSell d-block d-sm-table-cell text-center text-secondary p-0"
                                                       @if($unit->imageUrl())
                                                       style="background-image: url({{ $unit->imageUrl() }});"
                                                            @endif
                                                    >
                                                        <div class="h2">
                                                            <i class="fal fa-door-open"></i>
                                                        </div>
                                                    </span>

                                                    <div class="cardBodySell d-block d-sm-table-cell">
                                                        <div class="cardBody p-2">
                                                            <div class="ml-2">
                                                                <span class="h5">{{ $unit->name }}</span>
                                                            </div>
                                                            <div class="propCardSmallText">
                                                                <span data-toggle="modal" data-target="#confirmUnArchiveModal" data-record-id="{{ $unit->id }}" data-record-title="{{ $unit->name }}" >
                                                                    <button type="button" class="btn btn-sm btn-light text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Unarchive Unit"><i class="fal fa-box-open"></i> Unarchive Unit</button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-block d-sm-table-row">
                                                    <a href="{{ route('properties/units/edit', ['id' => $unit->id]) }}"
                                                        class="cardImgSell d-block d-sm-table-cell text-center text-secondary p-0"
                                                        @if($unit->imageUrl())
                                                            style="background-image: url({{ $unit->imageUrl() }});"
                                                        @endif
                                                    >
                                                        <div class="h2">
                                                            <i class="fal fa-door-open"></i>
                                                        </div>
                                                    </a>

                                                    <div class="cardBodySell d-block d-sm-table-cell">
                                                        <div class="cardBody p-2">
                                                            <div class="ml-2">
                                                                {{--<strong class="mr-3">unit {{ $key + 1 }}</strong>--}}
                                                                <span class="h5">{{ $unit->name }}</span>

                                                                @if ($unit->isOccupied())
                                                                    <span class="badge badge-danger">Occupied</span>
                                                                @else
                                                                    <span class="badge badge-success">Vacant</span>
                                                                @endif

                                                            </div>

                                                            <div class="propCardSmallText">
                                                                <a href="{{ route('properties/units/applications', ['unit' => $unit->id]) }}" title="Applications" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-file-signature"></i><span> Applications ({{$applicationCounts[($unit->id)]}})</span></a>
                                                                {{--}}<a href="{{ route('payments', ['property_id_unit_id' => $property->id . "_" . $unit->id]) }}" title="Payments" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-dollar-sign"></i><span> Payments</span></a>{{--}}
                                                                <a href="{{ route('properties/units/payments', ['unit' => $unit->id]) }}" title="Payments" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-dollar-sign"></i><span> Payments</span></a>
                                                                <a href="{{ route('properties/units/maintenance', ['unit' => $unit->id]) }}" title="Maintenance" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-tools"></i><span> Maintenance ({{$maintenanceCounts[($unit->id)]}})</span></a>
                                                                @if($unit->status)
                                                                    <a href="{{ route('leases/add', ['unit' => $unit->id]) }}" title="Move in Tenant" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-user-plus"></i><span> Move in Tenant</span></a>
                                                                @else
                                                                    <a href="{{ route('properties/units/leases', ['unit' => $unit->id]) }}" title="Leases" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-newspaper"></i><span> Leases</span></a>
                                                                @endif
                                                                <a href="{{ route('properties/units/expenses', ['unit_id' => $unit->id]) }}" title="Expenses" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-chart-pie-alt"></i><span>Expenses</span></a>
                                                                <a href="{{ route('properties/units/edit', ['id' => $unit->id]) }}" title="Edit" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-cog"></i><span> Edit</span></a>
                                                                <a href="javascript:void(0)" type="button" class="btn btn-sm btn-light text-muted" data-toggle="modal" data-target="#modalCopyUnit" data-id="{{ $unit->id }}" data-name="{{ $unit->name }}"><i class="fas fa-copy"></i> <span>Copy Unit</span></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (!old('units'))
                                @if (empty(Request::get('archived_units')))
                                    <div class="card-body pt-0">
                                        <div class="addUnitBox">
                                            <button class="btn btn-light btn-sm add-unit" type="button"><i class="fal fa-plus-circle mr-1"></i>add unit</button>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if (!!old('units'))
                                @for ($i = 0; $i < count(old('units')); $i++)
                                    <div class="card-body">
                                        <div class="card mb-3 unitForm unit-block">
                                            <div class="card-header bg-light cardHeaderMultiItem">
                                                <div class="unit-block-index">unit {{ $i + 1 }}</div>
                                                <a href="#" class="removeUnitButton float-right remove-item">
                                                    remove <i class="fal fa-times"></i>
                                                </a>
                                            </div>

                                            <div class="card-body bg-light">
                                                <div class="form-row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="unitNameFieldToSave{{ $i }}">
                                                            Nickname <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <input name="units[{{$i}}][name]" id="unitNameFieldToSave{{ $i }}" type="text" required="required" maxlength="255"
                                                            class="form-control @error('units.' . $i . '.name') is-invalid @enderror"
                                                            value="{{ old('units.' . $i . '.name') ?? '' }}"
                                                            data-name="name">
                                                        <span class="invalid-feedback" role="alert">
                                                            @error('units.' . $i . '.name')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="unitSquareFieldToSave{{ $i }}">
                                                            Sq. Footage
                                                        </label>

                                                        <input type="number" name="units[{{$i}}][square]" id="unitSquareFieldToSave{{ $i }}"
                                                            class="form-control @error('units.' . $i . '.square') is-invalid @enderror"
                                                            value="{{ old('units.' . $i . '.square') ?? '' }}"
                                                            data-name="square"
                                                            maxlength="6"
                                                            data-maxamount="999999"
                                                            data-type="integer">
                                                        <span class="invalid-feedback" role="alert">
                                                            @error('units.' . $i . '.square')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="unitBedroomsFieldToSave{{ $i }}">
                                                            Bedrooms <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <select name="units[{{$i}}][bedrooms]" id="unitBedroomsFieldToSave{{ $i }}" required="required"
                                                            class="custom-select form-control @error('units.' . $i . '.bedrooms') is-invalid @enderror"
                                                            data-name="bedrooms">
                                                            @for ($j = 1; $j < 11; $j++)
                                                                <option value="{{ $j }}"{{ old('units.' . $i . '.bedrooms') == $j ? ' selected' : '' }}>{{ $j }}</option>
                                                            @endfor
                                                        </select>
                                                        <span class="invalid-feedback" role="alert">
                                                            @error('units.' . $i . '.bedrooms')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="unitFullBathroomsFieldToSave{{ $i }}">
                                                            Full Bathrooms <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <select name="units[{{$i}}][full_bathrooms]" id="unitFullBathroomsFieldToSave{{ $i }}" required="required"
                                                            class="custom-select form-control @error('units.' . $i . '.full_bathrooms') is-invalid @enderror"
                                                            data-name="full_bathrooms">
                                                            <option hidden value=""></option>
                                                            @for ($j = 0; $j < 11; $j++)
                                                                <option value="{{ $j }}"{{ old('units.' . $i . '.full_bathrooms') == $j ? ' selected' : '' }}>{{ $j }}</option>
                                                            @endfor
                                                        </select>
                                                        <span class="invalid-feedback" role="alert">
                                                            @error('units.' . $i . '.full_bathrooms')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="unitHaltBathroomsFieldToSave{{ $i }}">
                                                            Half Bathrooms <i class="required fal fa-asterisk"></i>
                                                        </label>

                                                        <select name="units[{{$i}}][half_bathrooms]" id="unitHaltBathroomsFieldToSave{{ $i }}" required="required"
                                                            class="custom-select form-control @error('units.' . $i . '.half_bathrooms') is-invalid @enderror"
                                                            data-name="half_bathrooms">
                                                            <option hidden value=""></option>
                                                            @for ($j = 0; $j < 11; $j++)
                                                                <option value="{{ $j }}"{{ old('units.' . $i . '.half_bathrooms') == $j ? ' selected' : '' }}>{{ $j }}</option>
                                                            @endfor
                                                        </select>
                                                        <span class="invalid-feedback" role="alert">
                                                            @error('units.' . $i . '.half_bathrooms')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="unitInternalNotesFieldToSave{{ $i }}">Description</label>

                                                    <textarea name="units[{{$i}}][internal_notes]" id="unitInternalNotesFieldToSave{{ $i }}" class="form-control" rows="3" maxlength="65000"
                                                        data-name="internal_notes">{{ old('units.' . $i . '.internal_notes') ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div><!-- /unitForm -->

                                        <div class="addUnitBox">
                                            <button class="btn btn-light btn-sm add-unit" type="button">
                                                <i class="fal fa-plus-circle mr-1"></i> add unit
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            @endif

                            <div class="card-footer text-muted d-none" id="propertyFooter">
                                <a href="{{ route('properties/edit', ['id' => $property->id]) }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>

                                <button href="#" class="btn btn-primary btn-sm float-right" type="submit">
                                    <i class="fal fa-check-circle mr-1"></i> Save
                                </button>
                            </div>
                        </div><!-- /propertyForm -->
                    </form>

                </div>

                <div class="col-md-3 order-md-first">
                    @include('properties.edit-photos-partial')
                </div>

            </div>
        </div>

        <div style="display: none" class="hidden-block">
            <div class="card mb-3 unitForm unit-block">
                <div class="card-header bg-light cardHeaderMultiItem">
                    <div class="unit-block-index">unit {{ count($property->units) + 1 }}</div>
                    <a href="#" class="removeUnitButton float-right remove-item">remove <i class="fal fa-times"></i></a>
                </div>
                <div class="card-body bg-light">
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="unitNameField">
                                Nickname <i class="required fal fa-asterisk"></i>
                            </label>

                            <input name="units[0][name]" id="unitNameField" value="" type="text" required="required" maxlength="255"
                                class="form-control"
                                data-name="name">
                            <span class="invalid-feedback" role="alert"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="squareField">
                                Sq. Footage
                            </label>

                            <input type="text" name="units[0][square]" id="squareField" value=""
                                class="form-control"
                                data-type="integer"
                                maxlength="6"
                                data-maxamount="999999"
                                data-name="square">
                            <span class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="unitBedroomsField">
                                Bedrooms <i class="required fal fa-asterisk"></i>
                            </label>

                            <select name="units[0][bedrooms]" id="unitBedroomsField" required="required"
                                class="custom-select form-control"
                                data-name="bedrooms">
                                <option hidden value=""></option>
                                @for ($i = 1; $i < 11; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <span class="invalid-feedback" role="alert"></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="unitFullBathrooms">
                                Full Bathrooms <i class="required fal fa-asterisk"></i>
                            </label>

                            <select name="units[0][full_bathrooms]" id="unitFullBathrooms" required="required"
                                class="custom-select form-control"
                                data-name="full_bathrooms">
                                <option hidden value=""></option>
                                @for ($i = 0; $i < 11; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <span class="invalid-feedback" role="alert"></span>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="unitHaltBathroomsField">
                                Half Bathrooms <i class="required fal fa-asterisk"></i>
                            </label>

                            <select name="units[0][half_bathrooms]" id="unitHaltBathroomsField" required="required"
                                class="custom-select form-control"
                                data-name="half_bathrooms">
                                <option hidden value=""></option>
                                @for ($i = 0; $i < 11; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <span class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unitInternalNotesField">Internal Notes</label>

                        <textarea name="units[0][internal_notes]" id="unitInternalNotesField"
                            class="form-control" rows="3" maxlength="65000"
                            data-name="internal_notes"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modalCopyUnit">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form action="{{ route('properties/copy') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Copy Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input name="unit" id="copy_unit_id" type="hidden" value="">
                        <p>You are about to copy the <strong id="copy_unit_name"></strong></p>
                        <label for="copy_amount">Number of copies <i class="required fal fa-asterisk"></i></label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <button class="btn btn-secondary btnStepDown" type="button"><i class="fal fa-minus"></i></button>
                            </div>
                            <input name="copy" type="text" data-type="integer" data-maxamount="999" maxlength="3" class="form-control" id="copy_amount">
                            <div class="input-group-append">
                                <button class="btn btn-secondary btnStepUp" type="button"><i class="fal fa-plus"></i></button>
                            </div>
                        </div>
                        <span style="display: none;" id="copy_amount_error" class="invalid-feedback" role="alert">
                            Please enter the number of copies
                        </span>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-primary" id="modalCopySubmit"><i class="fas fa-copy"></i> Clone</button>
                    </div>
                </form>
            </div>
        </div>
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
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        jQuery(document).ready(function () {
            $('#modalCopyUnit').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#copy_unit_id').val(button.data('id'));
                modal.find('#copy_unit_name').text(button.data('name'));
                modal.find('#copy_amount_error').hide();
            });

            $('.btnStepUp').on('click', function (event) {
                var inp = $(this).parent().parent().find('input');
                var value = parseInt(inp.val()) ? parseInt(inp.val()) : 0;
                inp.val(value >= 999 ? 999 : value + 1);
            });
            $('.btnStepDown').on('click', function (event) {
                var inp = $(this).parent().parent().find('input');
                var value = parseInt(inp.val()) ? parseInt(inp.val()) : 0;
                inp.val(value <= 0 ? 0 : value - 1);
            });
            $('.btnStepDown').on('click', function (event) {});

            $("#modalCopySubmit").on('click', function (event) {
                const unit = $('#modalCopyUnit').find("#copy_unit_id").val();
                const copy = $('#modalCopyUnit').find("#copy_amount").val();
                if(copy > 0){
                    {{--
                    const form_data = new FormData();
                    form_data.append("_token", '{{ csrf_token() }}');
                    form_data.append("copy", copy);
                    form_data.append("unit", unit);

                    $.ajax({
                        url: "{{ route('properties/copy') }}",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'POST',
                        success: function (response) {
                            window.location.reload();
                        },
                        error: function () {
                            alert('Error!');
                        }
                    });
                    --}}
                } else {
                    event.preventDefault();
                    event.stopPropagation();
                    $('#copy_amount_error').show();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // $('.remove-item').hide();
            $('.remove-item').on(
                'click',
                function (e) {
                    e.preventDefault(false);
                    $(this).parent().parent().remove();
                    if ($('.unit-block').length === 1) {
                        $('.remove-item').hide();
                    }

                    var blocks = $('.unit-block');
                    for (var i = 0; i < blocks.length; i++) {
                        $(blocks[i]).find('.unit-block-index').text('unit ' + (i + 1 + {!! count($property->units) !!}));

                        var inputs = $(blocks[i]).find('input');
                        var selects = $(blocks[i]).find('select');
                        var textarea = $(blocks[i]).find('textarea');

                        for (var j = 0; j < inputs.length; j++) {
                            $(inputs[j]).val('');
                            $(inputs[j]).attr('name', 'units[' + i + '][' + $(inputs[j]).data('name') + ']');
                        }

                        for (var j = 0; j < selects.length; j++) {
                            $(selects[j]).val('');
                            $(selects[j]).attr('name', 'units[' + i + '][' + $(selects[j]).data('name') + ']');
                        }

                        for (var j = 0; j < textarea.length; j++) {
                            $(textarea[j]).val('');
                            $(textarea[j]).attr('name', 'units[' + i + '][' + $(textarea[j]).data('name') + ']');
                        }
                    }
                }
            );

            var sequence_number = 0;
            $($('.add-unit')[0]).on(
                'click',
                function () {
                    $('#propertyFooter').removeClass('d-none');

                    $("form").removeClass('was-validated');

                    var newBlock = $($('.hidden-block .unit-block')[0]).clone();
                    var inputs = newBlock.find('input');
                    var selects = newBlock.find('select');
                    var textarea = newBlock.find('textarea');
                    var labels = newBlock.find('label');

                    sequence_number++;

                    for (var i = 0; i < inputs.length; i++) {
                        $(inputs[i]).val('');
                        $(inputs[i]).attr('id', $(inputs[i]).attr('id') + sequence_number );
                    }

                    for (var i = 0; i < selects.length; i++) {
                        $(selects[i]).val('');
                        $(selects[i]).attr('id', $(selects[i]).attr('id') + sequence_number );
                    }

                    for (var i = 0; i < textarea.length; i++) {
                        $(textarea[i]).val('');
                        $(textarea[i]).attr('id', $(textarea[i]).attr('id') + sequence_number );
                    }

                    for (var i = 0; i < labels.length; i++) {
                        $(labels[i]).attr('for', $(labels[i]).attr('for') + sequence_number );
                    }

                    $(newBlock.find('.remove-item')[0]).on(
                        'click',
                        function (e) {
                            e.preventDefault(false);
                            $(this).parent().parent().remove();

                            var blocks = $('.unit-block');
                            if (blocks.length === 1) {
                                $('.remove-item').hide();
                            }

                            for (var i = 0; i < blocks.length; i++) {
                                $(blocks[i]).find('.unit-block-index').text('unit ' + (i + 1 + {!! count($property->units) !!}));
                            }
                        }
                    );

                    newBlock.find("input[data-type='integer']").on({
                        keyup: function() {
                            formatInteger(jQuery(this));
                        }
                    });

                    newBlock.find("input[data-maxamount]").on({
                        keyup: function() {
                            restrictMaxAmount(jQuery(this));
                        }
                    });

                    newBlock.insertBefore(this);

                    var blocks = $('.unit-block');
                    for (var i = 0; i < blocks.length; i++) {
                        $(blocks[i]).find('.unit-block-index').text('unit ' + (i + 1 + {!! count($property->units) !!}));

                        var inputs = $(blocks[i]).find('input');
                        var selects = $(blocks[i]).find('select');
                        var textarea = $(blocks[i]).find('textarea');

                        for (var j = 0; j < inputs.length; j++) {
                            $(inputs[j]).attr('name', 'units[' + i + '][' + $(inputs[j]).data('name') + ']');
                        }

                        for (var j = 0; j < selects.length; j++) {
                            $(selects[j]).attr('name', 'units[' + i + '][' + $(selects[j]).data('name') + ']');
                        }

                        for (var j = 0; j < textarea.length; j++) {
                            $(textarea[j]).attr('name', 'units[' + i + '][' + $(textarea[j]).data('name') + ']');
                        }
                    }
                    $('.remove-item').show();
                }
            );

            {{--}}
            $('#property_photo').on('change', function () {
                readFile(this);
            });

            $('#property_gallery').on('change', function () {
                readFiles(this);
            });

            $('.remove-property-image').on(
                'click',
                function (e) {
                    e.preventDefault();
                    $('#property_photo_container').attr('src', '');
                    $('#property_photo_remove').attr('checked', 'checked');
                }
            );
            {{--}}
        });
    </script>
    <script>
        $(document).ready(function() {
            $('input[name="last12Month"]').change(function(){
                var val = $( 'input[name="last12Month"]:checked' ).val();
                if(val == 1){
                    $('.last12Month1').removeClass('d-none');
                    $('.last12Month0').addClass('d-none');
                } else {
                    $('.last12Month0').removeClass('d-none');
                    $('.last12Month1').addClass('d-none');
                }
            });
            $('#last12Month1').click();

            // ARCHIVE RECORD
            $('#confirmUnArchiveModal').on('click', '.btn-ok', function(e) {
                var id = $(this).data('record-id');

                var form_data = new FormData();
                form_data.append("record_id", id);
                form_data.append("_token", '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('ajax_unit_unarchive') }}',
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
