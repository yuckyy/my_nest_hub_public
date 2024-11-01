@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('profile') }}">Admin</a> > <a href="{{ route('products') }}">Product Prices</a> > {{ isset($product) ? 'Edit Product' : 'Create Product' }}
    </div>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">{{ isset($product) ? 'Edit Product' : 'Create New Product' }}</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <form action="{{ route('products.save') }}" method="post">
                @csrf

                @if (isset($product))
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                @endif

                <div class="row">
                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="name">
                                {{ __('Product Name') }} <i class="required fal fa-asterisk"></i>
                            </label>

                            <input
                                id="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name') ? old('name') : (isset($product) ? $product->name : '') }}"
                                autocomplete="name"
                                autofocus
                            >

                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="max_units">
                                {{ __('Max Units Count') }} <i class="required fal fa-asterisk"></i>
                            </label>

                            <input
                                id="max_units"
                                type="text"
                                class="form-control @error('max_units') is-invalid @enderror"
                                name="max_units"
                                value="{{ old('max_units') ? old('max_units') : (isset($product) ? $product->max_units : '') }}"
                                autocomplete="max_units"
                                autofocus
                            >

                            @error('max_units')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="price">
                                {{ __('Price ($)') }} <i class="required fal fa-asterisk"></i>
                            </label>

                            <input
                                id="price"
                                type="text"
                                class="form-control @error('price') is-invalid @enderror"
                                name="price"
                                data-type="currency"
                                maxlength="6"
                                value="{{ old('price') ? old('price') : (isset($product) ? $product->price : '') }}"
                                autocomplete="price"
                            >
                            @error('price')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-6 pt-3">
                                @for ($i=0; $i < count($options); $i++)
                                    <div class="custom-control custom-checkbox pt-2 ml-2">
                                        <input type="checkbox" class="custom-control-input" name="options[]" value="{{ $options[$i]->id }}" id="option_{{ $options[$i]->id }}"
                                        {{ isset($product) && $product->hasOption($options[$i]->id) ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="option_{{ $options[$i]->id }}">{{ $options[$i]->name }}</label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10">
                        <div class="form-group mb-0">
                            <a href="{{ route('products') }}" class="btn btn-cancel pl-3 pr-4">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary pl-3 pr-4 float-right">{{ __('Save') }}</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    function formatCurrency(input, blur) {
        var input_val = input.val();
        if (input_val === "") { return; }
        var original_len = input_val.length;
        // initial caret position
        var caret_pos = input.prop("selectionStart");
        if (input_val.indexOf(".") >= 0) {
            var decimal_pos = input_val.indexOf(".");
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);
            left_side = formatNumber(left_side);
            right_side = formatNumber(right_side);
            if (blur === "blur") {
                right_side += "00";
            }
            right_side = right_side.substring(0, 2);
            input_val = left_side + "." + right_side;
        } else {
            input_val = formatNumber(input_val);
            if (blur === "blur") {
                input_val += ".00";
            }
        }
        input.val(input_val);

        if (blur !== "blur") {
            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }
    }

    jQuery(document).ready(function(){
        jQuery("input[data-type='currency']").each(function() {
            formatCurrency(jQuery(this));
        });

        jQuery("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency(jQuery(this));
            },
            blur: function() {
                formatCurrency(jQuery(this), "blur");
            }
        });
        jQuery("input[data-type='integer']").on({
            keyup: function() {
                formatInteger(jQuery(this));
            }
        });
    });
</script>
@endsection
