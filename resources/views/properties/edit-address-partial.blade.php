<div class="container-fluid">
    <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
        <div class="text-center text-sm-left">
            <div class="generalEditableBlock d-inline-block">
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
            {{--<h1 class="h2 d-inline-block fluidHeader">{{$property->address}}</h1>--}}
            <div class="pl-md-2 d-md-inline pt-3 pt-md-0">
                @if ($property->status() === 2)
                    <span class="badge badge-danger align-top{{ \Request::has('status') && \Request::get('status') != 0 ? ' d-none' : '' }}"><a href="{{ route('properties/edit', ['property' => $property->id]) }}{{ '?status=0' }}">Occupied</a></span>
                    <span class="badge badge-success align-top{{ \Request::has('status') && \Request::get('status') != 1 ? ' d-none' : '' }}"><a href="{{ route('properties/edit', ['property' => $property->id]) }}{{ '?status=1' }}">Vacant</a></span>
                    @if (\Request::has('status'))
                        <span class="badge badge-dark align-top"><a href="{{ url()->current() }}">View All</a></span>
                    @endif
                @endif

                @if ($property->status() === 1)
                    <span class="badge badge-success align-top">Vacant</span>
                @endif

                @if ($property->status() === 0)
                    <span class="badge badge-danger align-top">Occupied</span>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- EDIT DETAILS modal-->
<div class="modal fade" tabindex="-1" role="dialog" id="editDetailsModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">General Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('properties/edit-details-save',['id' => $property->id]) }}" class="needs-validation checkUnload" novalidate method="POST">
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
                    <a href="{{ route('properties/edit', ['id' => $property->id]) }}" class="btn btn-sm btn-cancel"><i class="fal fa-times mr-1"></i> Cancel</a>
                    {{--<button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>--}}
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>