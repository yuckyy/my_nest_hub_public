@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Properties</a>
    </div>
    <div class="container-fluid">

        @if (count(Auth::user()->properties) > 0)
            <div class="container-fluid">

                <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                    <div class="text-center text-sm-left">
                        <h1 class="h2 d-inline-block">Properties</h1>
                        <span class="badge badge-dark align-top">{{ count($properties) }} total</span>
                    </div>
                    <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                        <button
                            data-toggle="tooltip"
                            data-placement="top"
                            title="List view"
                            class="btn btn-sm btn-light mr-1 d-none d-md-inline-block"
                            type="button"
                            id="buttonListSw"
                        >
                            <i class="fal fa-th-list"></i>
                        </button>

                        {{--}}
                        <button
                            data-toggle="tooltip"
                            data-placement="top"
                            title="Import"
                            class="btn btn-sm btn-light mr-1 d-none d-md-inline-block"
                            type="button"
                            id="buttonImport"
                        >
                            <i class="fal fa-file-import"></i>
                        </button>
                        {{--}}

                        {{--}}
                        @if (count(Auth::user()->properties) > 0)
                        <a href="{{ route('properties', ["archived" => 1]) }}" class="btn btn-outline-secondary btn-sm mr-3"><i class="fal fa-file-archive mr-1"></i> View Archive</a>
                        @endif
                        {{--}}
@if (Auth::user()->archive == 0)
                            @if ($propertiesArchive !== 0 )
                                <button
                                    onclick="window.location='{{ route('properties', ["archived" => 1]) }}'"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="View Archived Properties"
                                    class="btn btn-sm btn-light mr-1 d-none d-md-inline-block"
                                    type="button"
                                >
                                    <i class="fal fa-file-archive mr-1"></i>
                                </button>
                            @endif

@endif
                        <form method="GET" class="input-group input-group-sm mr-3">
                            <input
                                name="address"
                                value="{{ \Request::has('address') ? \Request::get('address') : '' }}"
                                type="text"
                                class="form-control"
                                placeholder="Search by address"
                                aria-label="Search by address"
                                aria-describedby="button-addon2"
                            >
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="button-addon2" onclick="location.href='{{ route('properties') }}';">
                                    <i class="fal fa-times"></i>
                                </button>
                            </div>
                        </form>

                        <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                            <i class="fal fa-plus-circle mr-1"></i> Add New
                        </a>
                    </div>
                </div>

            </div>
        @else
            <div class="p-3">
                <div class="text-center text-sm-left pt-1 pb-2">
                    <h1 class="h2">Properties</h1>
                </div>

                <div class="card border-warning propertyForm">
                    <div class="card-body text-center alert-warning">
                        <p class="m-0">You didn't create any properties yet. Press "Add New Property" to create new property.</p>
                    </div>
                    <div class="card-footer border-warning text-muted text-center">
                        <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                            <i class="fal fa-plus-circle mr-1"></i> Add New Property
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="container-fluid">
            <div id="propsBox" class="row cardsBox">
                @if (count($properties) === 0)
                    @if (!empty(Request::get('address')))
                        <div class="propCardWrap col-12">
                            <p class="alert alert-info">
                                No matches found.
                            </p>
                        </div>
                    @endif
                @endif

                @foreach ($properties as $property)

                    <div class="propCardWrap col-lg-6">
                        <div class="propCard">
                            <div class="d-block d-sm-table propCardTable">
                                <div class="d-block d-sm-table-row">
                                    <a
                                        href="{{ route('properties/edit', ['id' => $property->id]) }}"
                                        class="cardImgSell d-block d-sm-table-cell text-center text-secondary p-3"
                                        @if($property->imageUrl())
                                            style="background-image: url({{ $property->imageUrl() }});"
                                        @endif
                                    >
                                        <div class="display-4">
                                            @if (!$property->image)
                                                {!! $property->icon() !!}
                                            @endif
                                        </div>

                                        @if ($property->type)
                                            <div class="h6">
                                                <strong>{{ $property->type->name }}</strong><br />
                                                @php( $unitCount = $property->units->where('archived',0)->count() )
                                                <small><i class="fal fa-eye ml-1"></i></small>
                                                {{ $unitCount . ' ' . Str::plural('unit', $unitCount) }}
                                            </div>
                                        @endif
                                    </a>

                                    <div class="cardBodySell d-block d-sm-table-cell">
                                        <div class="cardBody">
                                            <h5 class="ml-2 card-title">
                                                <div class="editableBox d-flex align-middle" data-toggle="modal" data-target="#editDetailsModal{{$property->id}}">
                                                    <button class="btn btn-lg btn-default p-0 mr-2">{{ $property->address ?? 'Edit Property' }} <i class="fas fa-pencil-alt text-muted" style="font-size: 14px"></i></button>
                                                </div>

                                                @if ($property->status() === 2)
                                                    <span class="badge badge-danger">Occupied ({{$property->vacantstatus()}})</span>
                                                    <span class="badge badge-success">Vacant ({{$property->occupiedstatus()}})</span>
                                                @endif

                                                @if ($property->status() === 1)
                                                    <span class="badge badge-success">Vacant ({{$property->occupiedstatus()}})</span>
                                                @endif

                                                @if ($property->status() === 0)
                                                    <span class="badge badge-danger">Occupied ({{$property->vacantstatus()}})</span>
                                                @endif
                                            </h5>

                                            <p class="ml-2 card-text">
                                                {{ $property->city }},
                                                @if ($property->state)
                                                    {{ $property->state->code }},
                                                @endif
                                                {{ $property->zip }}
                                            </p>

                                            <p class="card-text propCardSmallText">
                                                <a href="{{ route('applications') }}" data-toggle="tooltip" data-placement="top" title="Applications" class="btn btn-sm btn-light text-muted"><i class="fas fa-file-signature"></i><span> Applications ({{$allApplicationCountss[($property->id)]}})</span></a>
                                                <a href="{{ route('payments', ['property_id_unit_id' => $property->id . "_0"]) }}" data-toggle="tooltip" data-placement="top" title="Payments" class="btn btn-sm btn-light text-muted"><i class="fas fa-dollar-sign"></i><span> Payments</span></a>
                                                <a href="{{ route('maintenance', ['property_id_unit_id' => $property->id . "_0"]) }}" data-toggle="tooltip" data-placement="top" title="Maintenance" class="btn btn-sm btn-light text-muted"><i class="fas fa-tools"></i><span> Maintenance ({{$allMaintenanceCounts[($property->id)]}})</span></a>
                                                @if ($property->status() !== 0)
                                                    <a href="{{ route('leases/add', ['property' => $property->id]) }}" data-toggle="tooltip" data-placement="top" title="Move in Tenant" class="btn btn-sm btn-light text-muted"><i class="fas fa-user-plus"></i><span> Move in Tenant</span></a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- EDIT DETAILS modal-->
                    <div class="modal fade" tabindex="-1" role="dialog" id="editDetailsModal{{$property->id}}">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">General Information</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('properties/indexedit',['id' => $property->id]) }}" class="needs-validation checkUnload" novalidate method="POST">
                                    @csrf
                                    <div class="modal-body bg-light">

                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label for="typeField">
                                                    Property type <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <select name="type" class="custom-select @error('type') is-invalid @enderror" id="typeField">
                                                    <option hidden value=""></option>
                                                    @foreach ($types as $item)
                                                        <option value="{{ $item->id }}" {{ $item->id == $property->property_type_id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="invalid-feedback" role="alert" data-fieldname="property type">
                                                        @error('type')
                                                    {{ $message }}
                                                    @enderror
                                                    </span>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="addressField">
                                                    Address <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <input type="text" name="address" id="addressField" required="required" maxlength="255"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    value="{{ $property->address }}">
                                                <span class="invalid-feedback" role="alert" data-fieldname="address">
                                                        @error('address')
                                                    {{ $message }}
                                                    @enderror
                                                    </span>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cityField">
                                                    City <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <input type="text" id="cityField" name="city" required="required" maxlength="255"
                                                    class="form-control @error('city') is-invalid @enderror"
                                                    value="{{ $property->city }}">
                                                <span class="invalid-feedback" role="alert" data-fieldname="city">
                                                        @error('city')
                                                    {{ $message }}
                                                    @enderror
                                                    </span>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="stateField">
                                                    State <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <select name="state" id="stateField" required="required"
                                                        class="custom-select form-control @error('state') is-invalid @enderror">
                                                    <option hidden value=""></option>
                                                    @foreach ($states as $item)
                                                        <option value="{{ $item->id }}" {{ $item->id == $property->state_id ? 'selected' : '' }}>
                                                            {{ $item->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="invalid-feedback" role="alert" data-fieldname="state">
                                                        @error('state')
                                                    {{ $message }}
                                                    @enderror
                                                    </span>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="zipField">
                                                    Zip Code <i class="required fal fa-asterisk"></i>
                                                </label>
                                                <input name="zip" id="zipField" type="text" maxlength="5" required="required"
                                                    class="form-control @error('zip') is-invalid @enderror"
                                                    data-type="integer"
                                                    value="{{ $property->zip }}">
                                                <span class="invalid-feedback" role="alert" data-fieldname="zip">
                                                        @error('zip')
                                                    {{ $message }}
                                                    @enderror
                                                    </span>
                                            </div>
                                        </div>

                                        {{--
                                        <div class="form-row">
                                            <div class="col-md-4 mb-3">
                                                <label for="dateField">Date Purchased</label>

                                                <input id="dateField" name="date" type="date"
                                                    value="{{ substr($property->purchased, 0 , 10) }}"
                                                    class="form-control @error('date') is-invalid @enderror">
                                                <span class="invalid-feedback" role="alert">
                                                        @error('date')
                                                    {{ $message }}
                                                    @enderror
                                                    </span>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="purchased_amountField">Purchased Amount</label>

                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>

                                                    <input name="purchased_amount" id="purchased_amountField" type="text" maxlength="20"
                                                        class="form-control @error('purchased_amount') is-invalid @enderror"
                                                        data-type="currency"
                                                        data-maxamount="999999999"
                                                        value="{{ $property->purchased_amount }}">
                                                    <span class="invalid-feedback" role="alert">
                                                            @error('purchased_amount')
                                                        {{ $message }}
                                                        @enderror
                                                        </span>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="current_amountField">Current Value</label>

                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>

                                                    <input name="current_amount" id="current_amountField" type="text" maxlength="20"
                                                        class="form-control @error('current_amount') is-invalid @enderror"
                                                        data-type="currency"
                                                        data-maxamount="999999999"
                                                        value="{{ $property->value }}">
                                                    <span class="invalid-feedback" role="alert">
                                                            @error('current_amount')
                                                        {{ $message }}
                                                        @enderror
                                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                        --}}
                                    </div>
                                    <div class="modal-footer d-flex justify-content-between">
                                        <a href="{{ route('properties') }}" class="btn btn-sm btn-cancel"><i class="fal fa-times mr-1"></i> Cancel</a>
                                        {{--<button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>--}}
                                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>

            @if (count($properties) > 20)
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-caret-left"></i></a></li>
                        @for ($i = 1; $i < count($properties / 20) + 1; $i++)
                            <li class="page-item{{ $i === $page ? ' active' : ''}}">
                                <a class="page-link" href="#">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-caret-right"></i></a></li>
                    </ul>
                </nav>
            @endif

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        function setListView(){
            localStorage.view="listView";
            $("#propsBox").addClass("listView");
            var obj = $("#buttonListSw");
            obj.find("i").addClass("fa-th-large");
            obj.find("i").removeClass("fa-th-list");
            obj.attr("title", "Grid view");
            obj.attr("data-original-title", "Grid view");
            obj.tooltip('hide');
            $('.propCardSmallText').find("a").tooltip('enable');
        }

        function setGreedView(){
            localStorage.view="greedView";
            $("#propsBox").removeClass("listView");
            var obj = $("#buttonListSw");
            obj.find("i").removeClass("fa-th-large");
            obj.find("i").addClass("fa-th-list");
            obj.attr("title", "List view");
            obj.attr("data-original-title", "List view");
            obj.tooltip('hide');
            $('.propCardSmallText').find("a").tooltip('disable');
        }

        $( document ).ready(function() {
            $('.propCardSmallText').find("a").tooltip('disable');

            $("#buttonListSw").click(function(){
                $("#propsBox").toggleClass("listView");
                if($("#propsBox").hasClass("listView")){
                    setListView();
                } else {
                    setGreedView();
                }
                $("#buttonListSw").tooltip('show');
            });

            if(localStorage.view === "listView"){
                setListView();
            }

        });
    </script>

@endsection
