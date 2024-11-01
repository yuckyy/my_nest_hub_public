@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ url('properties') }}">Properties</a> > <a href="{{url('properties/add')}}">Add Property</a>
    </div>
    <div class="container-fluid pb-4">
        <form class="needs-validation checkUnload" novalidate method="POST" enctype="multipart/form-data">
            @csrf

            <div class="container-fluid">
                <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                    <h1 class="h2 text-center text-sm-left">Add Property</h1>

                    <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                        <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3">
                            <i class="fal fa-times mr-1"></i> Cancel
                        </a>

                        <button class="btn btn-primary btn-sm float-sm-right" type="submit" style="color: #fff">
                            <i class="fal fa-check-circle mr-1"></i> Save
                        </button>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">

                        <div class="card propertyFormPhoto">
                            <div class="card-header">
                                Property photo
                            </div>
                            <div class="propertyCardImageBox" id="propertyImagePlaceholder">
                            </div>
                            <div class="card-body text-center" id="propertyImageIcon">
                                <div class="display-2">
                                    <i class="fal fa-image text-white"></i>
                                </div>
                            </div>

                            <div class="card-footer text-muted text-center">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="property_photo" id="property_photo">

                                    <div class="custom-file-label btn btn-sm btn-primary" for="property_photo" data-browse="">
                                        <i class="fal fa-upload"></i> Upload photo
                                    </div>
                                </div>
                                <small>Recommended size: 400x400 pixels, 5Mb maximum</small>
                            </div>
                        </div><!-- /propertyFormPhoto -->

                    </div>

                    <div class="col-md-9">
                        <div class="card propertyForm">
                            <div class="card-header">General Information</div>

                            <div class="card-body bg-light">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="typeField">
                                            Property type <i class="required fal fa-asterisk"></i>
                                        </label>
                                        <select name="type" class="custom-select @error('type') is-invalid @enderror" id="typeField" required="required">
                                            <option hidden value=""></option>
                                            @foreach ($types as $item)
                                            <option value="{{ $item->id }}"{{ $item->id == old('type') ? ' selected' : ''}}>
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

                                        <input type="text" name="address" id="addressField" required="required" maxlength="255" placeholder=""
                                               class="form-control @error('address') is-invalid @enderror"
                                               value="{{old('address') ?? ''}}">
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
                                               value="{{old('city') ?? ''}}">
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
                                                <option value="{{ $item->id }}"{{ $item->id == old('state') ? ' selected' : ''}}>{{ $item->code }}</option>
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
                                               value="{{old('zip') ?? ''}}">
                                        <span class="invalid-feedback" role="alert" data-fieldname="zip">
                                            @error('zip')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>

                                {{--}}
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="dateField">Date Purchased</label>

                                        <input id="dateField" name="date" type="date"
                                               value="{{old('date') ?? ''}}"
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
                                                   value="{{old('purchased_amount') ?? ''}}">
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
                                                   value="{{old('current_amount') ?? ''}}">
                                            <span class="invalid-feedback" role="alert">
                                                @error('current_amount')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                {{--}}
                            </div>

                            <div class="card-header border-top cardHeaderMulti">
                                Unit Information
                            </div>

                            @if (!old('units'))
                            <div class="card-body">
                                <div class="card mb-3 unitForm unit-block">
                                    <div class="card-header bg-light cardHeaderMultiItem">
                                        <div class="unit-block-index">unit 1</div>
                                        <a href="#" class="removeUnitButton float-right remove-item">remove <i class="fal fa-times"></i></a>
                                    </div>
                                    <div class="card-body bg-light">
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label for="unitNameField">
                                                    Nickname <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <input type="text" name="units[0][name]" id="unitNameField" value="" required="required" maxlength="255"
                                                    class="form-control @error('units[][name]') is-invalid @enderror"
                                                    data-name="name">
                                                <span class="invalid-feedback" role="alert">
                                                    @error('units[][name]')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="unitSquareField">
                                                    Sq. Footage
                                                </label>

                                                <input type="text" name="units[0][square]" id="unitSquareField" value=""
                                                    class="form-control @error('units[0][square]') is-invalid @enderror"
                                                       data-name="square"
                                                       maxlength="6"
                                                       data-maxamount="999999"
                                                       data-type="integer">
                                                <span class="invalid-feedback" role="alert">
                                                    @error('units[][square]')
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

                                                <select name="units[0][bedrooms]" id="unitBedroomsField" required="required"
                                                    class="custom-select form-control @error('units[0][bedrooms]') is-invalid @enderror"
                                                    data-name="bedrooms">
                                                    <option hidden value=""></option>
                                                    @for ($i = 1; $i < 11; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <span class="invalid-feedback" role="alert">
                                                    @error('units[][bedrooms]')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="unitFullBathroomsField">
                                                    Full Bathrooms <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <select name="units[0][full_bathrooms]" id="unitFullBathroomsField" required="required"
                                                    class="custom-select form-control @error('units[0][full_bathrooms]') is-invalid @enderror"
                                                    data-name="full_bathrooms">
                                                    <option hidden value=""></option>
                                                    @for ($i = 0; $i < 11; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <span class="invalid-feedback" role="alert">
                                                    @error('units[][full_bathrooms]')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="unitHaltBathroomsField">
                                                    Half Bathrooms <i class="required fal fa-asterisk"></i>
                                                </label>

                                                <select name="units[0][half_bathrooms]" id="unitHaltBathroomsField" required="required"
                                                    class="custom-select form-control"
                                                    data-name="half_bathrooms">
                                                    {{--}}<option hidden value=""></option>{{--}}
                                                    @for ($i = 0; $i < 11; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <span class="invalid-feedback" role="alert">
                                                    @error('units[][half_bathrooms]')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="unitInternalNotesField">Internal Notes</label>
                                            <textarea name="units[0][internal_notes]" id="unitInternalNotesField" class="form-control" rows="3" data-name="internal_notes" maxlength="65000"></textarea>
                                        </div>
                                    </div>
                                </div><!-- /unitForm -->

                                <div class="addUnitBox">
                                    <button class="btn btn-light btn-sm add-unit" type="button"><i class="fal fa-plus-circle mr-1"></i>add unit</button>
                                </div>
                            </div>
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

                                                    <select name="units[{{$i}}][bedrooms]" id="unitBedroomsFieldToSave{{ $i }}" required="required" maxlength="255"
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
                                                    <label for="unitFfullBathroomsFieldToSave{{ $i }}">
                                                        Full Bathrooms <i class="required fal fa-asterisk"></i>
                                                    </label>

                                                    <select name="units[{{$i}}][full_bathrooms]" id="unitFfullBathroomsFieldToSave{{ $i }}" required="required"
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
                                                <label for="unitDescriptionFieldToSave{{ $i }}">Description</label>

                                                <textarea name="units[{{$i}}][description]" id="unitDescriptionFieldToSave{{ $i }}" class="form-control" rows="3"
                                                          data-name="description" maxlength="65000">{{ old('units.' . $i . '.description') ?? '' }}</textarea>
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
                            <div class="card-footer text-muted">
                                <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>

                                <button href="#" class="btn btn-primary btn-sm float-right" type="submit">
                                    <i class="fal fa-check-circle mr-1"></i> Save
                                </button>
                            </div>
                        </div><!-- /propertyForm -->

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#propertyImagePlaceholder').html('<img src="'+e.target.result+'" class="card-img-top">');
                    $('#propertyImageIcon').addClass('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        {{--
        // !!!! reserved for gallery (just in case). please don't remove yet
        function readFiles(input) {
            $('#property_gallery_images').html('');
            if (input.files) {
                for (var i = 0; i < input.files.length; i++) {
                    console.log(i);
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        var img = $(document.createElement('img'));
                        img.attr('alt', '');
                        img.css('max-width', '100px');
                        $('#property_gallery_images').append(img);
                        img.attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[i]);
                }
            }
        }
        $(document).ready(function() {
            $('#property_gallery').on('change', function () {
                readFiles(this);
            });
        });
        --}}

        $(document).ready(function() {
            $('.remove-item').hide();
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
                        $(blocks[i]).find('.unit-block-index').text('unit ' + (i + 1));

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
                    $("form").removeClass('was-validated');
                    var newBlock = $($('.unit-block')[0]).clone();
                    var inputs = newBlock.find('input');
                    var selects = newBlock.find('select');
                    var textarea = newBlock.find('textarea');
                    var labels = newBlock.find('label');

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
                                $(blocks[i]).find('.unit-block-index').text('unit ' + (i + 1));
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
                        $(blocks[i]).find('.unit-block-index').text('unit ' + (i + 1));

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

            $('#property_photo').on('change', function () {
                readFile(this);
            });
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_AUTOCOMPLETE_API_KEY') }}&libraries=places"></script>
    <script>
        var placeSearch, autocomplete, street_number, route, locality, administrative_area_level_1, postal_code;
        $(document).ready(function() {
            {{--
            // Create the autocomplete object, restricting the search predictions to
            // geographical location types.
            --}}
            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById("addressField"),
                {
                    types: ["geocode"],
                    componentRestrictions: {country: 'us'}
                }
            );
            {{--
            // Avoid paying for data that you don't need by restricting the set of
            // place fields that are returned to just the address components.
            --}}
            autocomplete.setFields(["address_component"]);
            {{--
            // When the user selects an address from the drop-down, populate the
            // address fields in the form.
            --}}
            autocomplete.addListener("place_changed", fillInAddress);
        });

        function fillInAddress() {
            const place = autocomplete.getPlace();
            for (const component of place.address_components) {
                const addressType = component.types[0];
                if(addressType === 'street_number'){
                    street_number = component['short_name']
                }
                if(addressType === 'route'){
                    route = component['long_name']
                }
                if(addressType === 'locality'){
                    locality = component['long_name']
                }
                if(addressType === 'administrative_area_level_1'){
                    administrative_area_level_1 = component['short_name']
                }
                if(addressType === 'postal_code'){
                    postal_code = component['short_name']
                }
            }
            $("#addressField").val((street_number ? street_number : '') + ' ' + (route ? route : ''));
            $("#cityField").val(locality);
            $("#stateField option").filter(function() {
                return $(this).text() == administrative_area_level_1;
            }).prop('selected', true);
            $("#zipField").val(postal_code);
        }
    </script>
@endsection
