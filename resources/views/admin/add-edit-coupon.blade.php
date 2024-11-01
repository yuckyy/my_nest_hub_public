@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('profile') }}">Admin</a> > <a href="{{ route('coupons') }}">Coupons</a> > {{ isset($coupon) ? 'Edit Coupon' : 'Create Coupon' }}
    </div>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">{{ isset($coupon) ? 'Edit Coupon' : 'Create New Coupon' }}</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session()->get('error') }}
                </div>
            @endif
            <form action="{{ route('coupons.save') }}" method="post">
                @csrf

                @if (isset($coupon))
                    <input type="hidden" name="coupon_id" value="{{ $coupon->id }}">
                @endif

                <div class="row">
                    <div class="col-sm-4">

                        <div class="form-group">
                            <label for="name">
                                {{ __('Coupon Name') }} <i class="required fal fa-asterisk"></i>
                            </label>

                            <input
                                id="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name') ? old('name') : (isset($coupon) ? $coupon->name : '') }}"
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
                            <label for="code">
                                {{ __('Coupon Code') }} <i class="required fal fa-asterisk"></i>
                            </label>

                            <input
                                id="code"
                                type="text"
                                class="form-control @error('code') is-invalid @enderror"
                                name="code"
                                value="{{ old('code') ? old('code') : (isset($coupon) ? $coupon->code : '') }}"
                                autocomplete="code"
                            >
                            @error('code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="discount">
                                {{ __('Discount ($)') }} <i class="required fal fa-asterisk"></i>
                            </label>

                            <input
                                id="discount"
                                type="text"
                                class="form-control @error('discount') is-invalid @enderror"
                                name="discount"
                                data-type="currency"
                                maxlength="5"
                                value="{{ old('discount') ? old('discount') : (isset($coupon) ? $coupon->discount : '') }}"
                                autocomplete="discount"
                            >
                            @error('discount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <a href="{{ route('coupons') }}" class="btn btn-cancel pl-3 pr-4">{{ __('Cancel') }}</a>
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
