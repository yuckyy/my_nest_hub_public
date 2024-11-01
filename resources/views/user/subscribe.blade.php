@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/membership') }}">Membership</a> > <a href="#">Subscribe</a>
    </div>
    <div class="container pb-4">
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
        <div class="row justify-content-md-center">
            <div class="col-lg-12">
                <div class="container">
                    <div class="pt-4 pb-3">
                        <h1 class="h2 text-center text-sm-left">Subscribe to {{ $plan->name }} Plan</h1>
                    </div>
                </div>
                <div class="container">
                    <form class="needs-validation finance-form" method="post" action="{{ route('profile/create-subscription') }}">
                        @csrf

                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="coupon_applied" id="coupon_applied" value="{{ old('coupon_applied') }}">
                        <input type="hidden" name="newFinanceAccount" value="">
                        <div class="card propertyForm">
                            <div class="card-header">
                                <i class="fal fa-credit-card mr-2"></i> Your Financial Account
                            </div>

                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="inRowComment"><i class="fal fa-info-circle"></i> Select your financial account.</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="h4">Your total: $<span id="total">{{ $plan->price }}</span></p>
                                        @if(empty(Auth::user()->activePlan()))
                                            <div class="inRowComment text-primary2">
                                                <i class="fal fa-info-circle text-primary2"></i> Your card will not be charged during trial period.
                                            </div>
                                        @endif

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="financeAccount">Financial Account <i class="required fal fa-asterisk"></i></label>
                                            <select name="financeAccount" class="form-control @error('newFinanceAccount') is-invalid @enderror custom-select fixedMaxInputWidth" id="financeAccount" required>
                                                <option value="" hidden>Choose an account</option>
                                                @foreach (Auth::user()->financialSubscribeAccounts() as $f)
                                                    <option value="{{ $f->id }}">{{ $f->nickname }}</option>
                                                @endforeach
                                                <option value="_new" {{ count(Auth::user()->financialSubscribeAccounts()) == 0 ? 'selected' : '' }}>Add Financial Account</option>
                                            </select>
                                            @error('newFinanceAccount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3 coupon-code">
                                            <label for="couponCode">Coupon code</label>
                                            <div class="form-inline">
                                                <input type="text" class="form-control @error('coupon_code') is-invalid @enderror" name="coupon_code" id="couponCode" value="{{ old('coupon_code') }}">
                                                <button type="button" class="btn btn-secondary" id="apply-code">Apply Code</button>
                                            </div>
                                            <span class="coupon_code-error text-danger"></span>
                                            <span class="coupon_code-success text-success"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="collapse multi-collapse" id="addFinanceAccountContent">
                                    @component('includes.finance.add_subscribe_account_form')
                                    @endcomponent
                                </div>

                            </div>

                            <div class="card-footer text-muted">
                                <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>

                                <button
                                        type="button"
                                        role="submit"
                                        class="btn btn-primary btn-sm float-right btn-submit"
                                >
                                    <i class="fal fa-check-circle mr-1"></i> Submit
                                </button>
                            </div>
                        </div><!-- /propertyForm -->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function() {
            if ($("#financeAccount").val() === "_new"){
                $("#addFinanceAccountContent").collapse('show');
            } else {
                $("#addFinanceAccountContent").collapse('hide');
            }
            $("#financeAccount").change(function(){
               var val = $(this).val();
               if (val === "_new"){
                $("#addFinanceAccountContent").collapse('show');
               } else {
                   $("#addFinanceAccountContent").collapse('hide');
               }
            });

        });
        $('#financeAccount').change(function(){
            if ($(this).val() != '_new') {
                $('input[name=newFinanceAccount]').val($(this).val());
            }
        })
        $('.btn-submit').click(function () {
            var form = $(this).closest('form');
            if ($("#financeAccount").val() != '_new') {
                form.submit();
            } else {
                var f = $('.financeSwitchContent:visible');
                ajaxSubmit(f);
            }
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#apply-code').click(function(){
            var code = $('input[name=coupon_code]').val(),
                error = $('.coupon_code-error'),
                success = $('.coupon_code-success');

            $.post("{{ route('apply-code') }}", { code: code,total:{{ $plan->price }} }, function (data) {
                if (data.error) {
                    success.html('');
                    error.html(data.error);
                    $('#total').html(parseFloat(data.old_total).toFixed(2));
                    $('#coupon_applied').val('');
                } else {
                    success.html(data.success);
                    error.html('');
                    $('#total').html(parseFloat(data.new_total).toFixed(2));
                    $('#coupon_applied').val(code);
                }
            });
        })
        function ajaxSubmit(form) {
            $(form).find('.invalid-feedback strong').html('');
            $(form).find('.error-message').html('');
            $('.form-control').removeClass('is-invalid');
            $('.btn-submit').prop('disabled',true);
            $(".preloader").fadeIn("slow");
            $.ajax({
                url     : $(form).find('input[name=form-action]').val(),
                type    : 'post',
                data    : $(form).find(':input').serialize(),
                dataType: 'json',
                success : function (json) {
                    $('.btn-submit').prop('disabled',false);
                    $(".preloader").fadeOut("slow");
                    var finance_id = json.finance_id;
                    $('input[name=newFinanceAccount]').val(finance_id);
                    $('.finance-form').submit();
                },
                error: function(json) {
                    $('.btn-submit').prop('disabled',false);
                    $(".preloader").fadeOut("slow");
                    $(form).find('.invalid-feedback strong').html('');
                    $(form).find('.error-message').html('');
                    $('.form-control').removeClass('is-invalid');
                    if(json.status === 422) {
                        var errors = json.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('.'+key+'-error').closest('div').find('.form-control').addClass('is-invalid');
                            $('.'+key+'-error').html(value);
                        });
                    } else {
                        $(form).find('.invalid-feedback strong').html('');
                        $('.form-control').removeClass('is-invalid');
                        $(form).find('.error-message').html(json.responseJSON.message);
                    }
                }
            });
        }
    </script>
@endsection
