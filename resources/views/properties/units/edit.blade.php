@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')
    <div class="container-fluid pb-4">

        {{--@if (session('success'))
            <div class="mt-3">
                <div class="alert alert-success mb-0" role="alert">
                    {{ session()->get('success') }}
                </div>
            </div>
        @endif--}}

        <form class="needs-validation checkUnload" novalidate method="POST">
            @csrf
            <div class="container-fluid">
                <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                    @include('properties.units.header-partial')

                    <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                        <a href="{{ route("properties/edit",['id'=>$unit->property_id]) }}" class="btn btn-cancel btn-sm mr-3">
                            <i class="fal fa-times mr-1"></i> Cancel
                        </a>

                        <button class="btn btn-primary btn-sm float-sm-right" type="submit" style="color: #fff">
                            <i class="fal fa-check-circle mr-1"></i> Save
                        </button>
                    </div>
                </div>
            </div>
            <div class="container-fluid unitFormContainer">

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {!! session('error') !!}
                    </div>
                @endif

                <div class="row">
                    <div class="navTabsLeftContainer col-md-3">
                        @include('includes.units.menu')
                    </div>

                    <div class="navTabsLeftContent col-md-9">

                        <div class="card propertyForm propertyFormGeneralInfo">
                            <div class="card-body bg-light">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nameField">
                                            Nickname <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <input type="text" name="name" id="nameField" required="required" maxlength="255"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ $unit->name }}"
                                            data-name="name">
                                        <span class="invalid-feedback" role="alert">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="squareField">
                                            Sq. Footage
                                        </label>

                                        <input type="text" name="square" id="squareField"
                                            class="form-control @error('square') is-invalid @enderror"
                                            value="{{ $unit->square }}"
                                            data-type="integer"
                                            maxlength="6"
                                            data-maxamount="999999"
                                            data-name="square">
                                        <span class="invalid-feedback" role="alert">
                                            @error('square')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="unitBedroomsField">
                                            Bedrooms <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <select name="bedrooms"
                                            class="custom-select form-control @error('bedrooms') is-invalid @enderror"
                                            id="unitBedroomsField"
                                            data-name="bedrooms">
                                            <option hidden value=""></option>
                                            @for ($j = 1; $j < 11; $j++)
                                                <option
                                                    value="{{ $j }}"
                                                    {{$unit->bedrooms == $j ? 'selected' : ''}}
                                                >
                                                    {{ $j }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span class="invalid-feedback" role="alert">
                                            @error('bedrooms')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="unitFullBathrooms">
                                            Full Bathrooms <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <select name="full_bathrooms" id="unitFullBathrooms" required="required"
                                            class="custom-select form-control @error('full_bathrooms') is-invalid @enderror"
                                            data-name="full_bathrooms">
                                            <option hidden value=""></option>
                                            @for ($j = 0; $j < 11; $j++)
                                                <option
                                                    value="{{ $j }}"
                                                    {{$unit->full_bathrooms == $j ? 'selected' : ''}}
                                                >
                                                    {{ $j }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span class="invalid-feedback" role="alert">
                                            @error('full_bathrooms')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="unitHaltBathroomsField">
                                            Half Bathrooms <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <select name="half_bathrooms" id="unitHaltBathroomsField" required="required"
                                            class="custom-select form-control @error('half_bathrooms') is-invalid @enderror"
                                            data-name="half_bathrooms">
                                            <option hidden value=""></option>
                                            @for ($j = 0; $j < 11; $j++)
                                                <option
                                                    value="{{ $j }}"
                                                    {{$unit->half_bathrooms == $j ? 'selected' : ''}}
                                                >
                                                    {{ $j }}
                                                </option>
                                            @endfor
                                        </select>
                                        <span class="invalid-feedback" role="alert">
                                            @error('half_bathrooms')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="unitInternalNotesField">Internal Notes</label>
                                    <textarea name="internal_notes" id="unitInternalNotesField" rows="3" maxlength="65000"
                                              class="form-control">{{ $unit->internal_notes }}</textarea>
                                </div>
                            </div>

                            <div class="card-footer text-muted">
                                <a href="{{ route("properties/edit",['id'=>$unit->property_id]) }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>

                                <button class="btn btn-primary btn-sm float-right" type="submit">
                                    <i class="fal fa-check-circle mr-1"></i> Save
                                </button>
                            </div>
                        </div><!-- /propertyForm -->

                        @if($canDelete)
                            <div class="card mt-4 propertyForm">
                                <div class="card-body">
                                    <h3>Archive Unit</h3>
                                    <div class="inRowComment">
                                        <i class="fal fa-info-circle"></i> This will archive unit, all its leases, applications, transactions and maintenance.
                                    </div>

                                    <button type="button" class="btn btn-secondary btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmArchivePropertyModal">
                                        <i class="fal fa-file-archive mr-1"></i> Archive
                                    </button>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header alert alert-danger text-danger mb-0">
                                    <strong>Danger Zone</strong>
                                </div>
                                <div class="card-body">
                                    <h3>Delete Unit</h3>
                                    <div class="inRowComment">
                                        <i class="fal fa-info-circle"></i> This will delete unit, all its leases, applications, transactions and maintenance. <span class="text-danger">This operation cannot be undone.</span>
                                    </div>

                                    <button type="button" class="btn btn-danger btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmDeleteUnitModal">
                                        <i class="fal fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="confirmArchivePropertyModal" tabindex="-1" role="dialog" aria-labelledby="confirmArchivePropertyModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmArchivePropertyModalTitle">Confirm Archive Property</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>Are you sure you want to archive the unit <strong>{{$unit->name}}</strong>?<br /> This will archive:</p>
                    <ul>
                        <li>Unit</li>
                        <li>Archive Leases</li>
                        <li>Archive Transactions</li>
                        <li>Archive Maintenance</li>
                        <li>Archive Applications</li>
                    </ul>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> Cancel
                    </button>
                    <form action="{{ route('properties/units/operations-save', ['id' => $unit->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="archive" value="1">
                        <button class="btn btn-secondary btn-sm mr-3" type="submit">
                            <i class="fal fa-file-archive mr-1"></i> Yes, Archive
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteUnitModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteUnitModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteUnitModalTitle">Confirm Delete Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>Are you sure you want to delete the unit <strong>{{$unit->name}}</strong>?<br /> This will delete:</p>
                    <ul>
                        <li>Unit</li>
                        <li>Delete Leases</li>
                        <li>Delete Transactions</li>
                        <li>Delete Maintenance</li>
                        <li>Delete Applications</li>
                    </ul>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> Cancel
                    </button>
                    <form action="{{ route('properties/units/operations-save', ['id' => $unit->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="delete" value="1">
                        <button class="btn btn-danger btn-sm mr-3" type="submit">
                            <i class="fal fa-trash-alt mr-1"></i> Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
@endsection
