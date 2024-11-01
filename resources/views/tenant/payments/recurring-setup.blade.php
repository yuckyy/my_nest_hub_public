@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('payments') }}">Payments</a> > <a href="#">Recurring Payment Setup</a>
    </div>
    <div class="container-fluid pb-4 pt-4">

        <div class="container-fluid pb-2">
            <h1 class="h2 text-center text-sm-left">Recurring Payment Setup</h1>
        </div>

        <div class="container-fluid">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <div class="row">
                <div class="col-lg-8">
                    <div class="card propertyForm mb-4">

                        <form id="processPaymentForm" class="needs-validation finance-form" action="{{ route('recurring-payment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="newFinanceAccount" value="">
                            <!--
                            <input type="hidden" name="amount" value="{{ $subtotal }}">
                            <input type="hidden" name="processingFee" value="{{ $fee }}">
                            -->
                            <input type="hidden" name="lease" value="{{ \Request::get('lease') }}">
                            @foreach ($invoices as $i)
                                <input type="hidden" name="invoice[]" value="{{ $i->id }}">
                            @endforeach

                            <div class="row no-gutters">
                                <div class="col-md-6 border-right bg-light border-right">
                                    <div class="card-header">
                                        Payment Method
                                    </div>
                                    <div class="card-body bg-light pb-2">
                                        <div class="mb-3">
                                            <label for="financeAccount">Financial Account</label>
                                            <select name="financeAccount" class="custom-select fixedMaxInputWidth" id="financeAccount" required>
                                                {{--}}
                                                Check if landlord connected stripe of dwolla account and select related to it from the tenant
                                                {{--}}
                                                @if($lease->landlordLinkedFinanceStripe())
                                                    @foreach (Auth::user()->financialAccounts as $f)
                                                        @if(($f->finance_type == 'bank') || ($f->finance_type == 'card'))
                                                            <option value="{{ $f->id }}">{{ $f->nickname }}</option>
                                                        @endif
                                                    @endforeach
                                                @elseif($lease->landlordLinkedFinanceDwolla())
                                                    @foreach (Auth::user()->financialAccounts as $f)
                                                        @if($f->finance_type == 'dwolla_source')
                                                            <option value="{{ $f->id }}">{{ $f->nickname }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <option value="_new">Setup New Financial Account</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 bg-light rightCardColumn">
                                    <div class="card-header">
                                        Date My Payment will be deducted from my banking account
                                    </div>
                                    <div class="card-body">
                                        <label for="recurringPaymentDay" class="inLabelComment">
                                            <i class="fas fa-info-circle mr-1 text-secondary"></i> According to your lease, your payment due date is {{ $lease->monthly_due_date }} of each month.
                                        </label>
                                        <select name="recurring_payment_day" id="recurringPaymentDay" class="form-control">
                                            @for($day = 1; $day <= 31; $day++)
                                                <option value="{{ $day }}" {{ old('recurring_payment_day') && old('recurring_payment_day') == $day ? 'selected' : "" }}
                                                            data-late={{ $day >= ($lease->monthly_due_date + $lease->late_fee_day) ? '1' : '0' }} @if($day>28) disabled @endif >{{ $day }}</option>
                                            @endfor
                                        </select>
                                        <div class="mt-1 ml-1 recurringPaymentDay text-primary2" style="visibility: hidden;">
                                            <small>
                                                <i class="fas fa-exclamation-circle mr-1 text-primary2"></i> You will pay for the next month
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Financial account form -->
                        <div class="collapse multi-collapse" id="addFinanceAccountContent">
                            @component('includes.finance.add_finance_account_form_tenant',['newLease' => true, 'lease' => $lease])
                            @endcomponent
                        </div>
                        <!-- /Financial account form (end) -->

                        <!-- submit button for preset financial account -->
                        <div class="card-footer text-muted text-center commonSubmitFooter">
                            <button type="button" class="btn btn-primary btn-lg m-2 btn-submit"{{ !$lease->landlordLinkedFinance() ? 'disabled' : '' }}><i class="fal fa-check mr-2"></i> SETUP RECURRING PAYMENT</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card bg-light propertyForm mb-4">
                        <div class="card-header">
                            Recurring Payment Summary
                        </div>
                        @if (count($invoices) != 0)
                            <div class="card-body">
                                <table class="table snippetTable">
                                    <tbody>
                                        @foreach ($invoices as $i)
                                        <tr>
                                            <td>{{ $i->description }}:</td>
                                            <td>{{ \Carbon\Carbon::parse($i->recurring_payment_day)->format('M d, Y') }}</td>
                                            <td class="text-right">${{ $i->bill_amount }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="2">Subtotal:</th>
                                            <th class="text-right">${{ number_format($subtotal, 2, '.', '') }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2">
                                                <div class="stripeDisplay">
                                                    Processing Fee
                                                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title="2.9% + $0.30 Credit Card Processing Fee">
                                                    <i class="fal fa-question-circle ml-1 text-secondary"></i>
                                                </span>
                                                </div>
                                                <div class="stripeAchDisplay" style="display: none;">
                                                    Processing Fee
                                                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title="0.8% ($5 maximum) Stripe Processing Fee">
                                                        <i class="fal fa-question-circle ml-1 text-secondary"></i>
                                                    </span>
                                                </div>
                                                <div class="dwollaAchDisplay" style="display: none;">
                                                    Processing Fee
                                                    <span data-toggle="tooltip" data-placement="top" title="" data-original-title="0.8% (5&cent; minimum $5 maximum) Banking Processing Fee">
                                                        <i class="fal fa-question-circle ml-1 text-secondary"></i>
                                                    </span>
                                                </div>
                                            </th>
                                            <th class="text-right">
                                                <div class="stripeDisplay">
                                                    ${{ $fee }}
                                                </div>
                                                <div class="stripeAchDisplay" style="display: none;">
                                                    ${{ $stripeAchDdFee }}
                                                </div>
                                                <div class="dwollaAchDisplay" style="display: none;">
                                                    ${{ $dwollaAchFee }}
                                                </div>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-body bg-white border-top">
                                <div class="m-2 pb-2 text-center">
                                    <div class="h5 m-1">
                                        Total:
                                    </div>
                                    <div class="h2 m-0 text-danger">
                                        <div class="stripeDisplay">
                                            <span class="text-primary2">$</span>{{ $total }}
                                        </div>
                                        <div class="stripeAchDisplay" style="display: none;">
                                            <span class="text-primary2">$</span>{{ $stripeAchDdTotal }}
                                        </div>
                                        <div class="dwollaAchDisplay" style="display: none;">
                                            <span class="text-primary2">$</span>{{ $dwollaAchTotal }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card-body">
                                There are no invoices yet
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var financialAccounts = [];
        @foreach (Auth::user()->financialAccounts as $f)
            financialAccounts['{{ $f->id }}'] = '{{ $f->finance_type }}';
        @endforeach
        function switchFeesAmounts(sourceAccountType){
            if(sourceAccountType == 'card'){
                $('.stripeDisplay').show();
                $('.stripeAchDisplay').hide();
                $('.dwollaAchDisplay').hide();
            } else if(sourceAccountType == 'bank'){
                $('.stripeDisplay').hide();
                $('.stripeAchDisplay').show();
                $('.dwollaAchDisplay').hide();
            } else if(sourceAccountType == 'dwolla_source'){
                $('.stripeDisplay').hide();
                $('.stripeAchDisplay').hide();
                $('.dwollaAchDisplay').show();
            } else {
                //Something wrong
                $('.stripeDisplay').hide();
                $('.stripeAchDisplay').hide();
                $('.dwollaAchDisplay').hide();
            }
        }
        function switchCommonFooter(sourceAccountType){
            if(sourceAccountType == 'card'){
                $('.commonSubmitFooter').show();
            } else if(sourceAccountType == 'bank'){
                $('.commonSubmitFooter').hide();
            } else if(sourceAccountType == 'dwolla_source'){
                $('.commonSubmitFooter').hide();
            } else {
                //Something wrong
                $('.commonSubmitFooter').hide();
            }
        }

        $(document).ready(function() {
            var val = $("#financeAccount").val();
            if (val === "_new"){
                $("#addFinanceAccountContent").collapse('show');
                $('input[type="radio"][name="financeSwitch"]').first().prop('checked',true);
                var newAccountType = $('input[name=financeSwitch]').val();
                switchFeesAmounts(newAccountType);
                switchCommonFooter(newAccountType);
            } else {
                $("#addFinanceAccountContent").collapse('hide');
                switchFeesAmounts(financialAccounts[val]);
            }
            if ($('#addFinanceAccountContent .is-invalid').length > 0) {
                $('#addFinanceAccountContent').collapse('show');
            }
            $("#financeAccount").change(function(){
                var val = $(this).val();
                if (val === "_new"){
                    $('input[type="radio"][name="financeSwitch"]').first().prop('checked',true);
                    $("#addFinanceAccountContent").collapse('show');
                    $('#financeSwitchContent2').hide();
                    $('#financeSwitchContent1').show();
                    var newAccountType = $('input[name=financeSwitch]').val();
                    switchFeesAmounts(newAccountType);
                    switchCommonFooter(newAccountType);
                } else {
                    //already setup accounts
                    $("#addFinanceAccountContent").collapse('hide');
                    switchFeesAmounts(financialAccounts[val]);
                    $('.commonSubmitFooter').show();
                }

            });

            $('#recurringPaymentDay').change(function() {
                if ($(this).find('option:selected').data('late') == 1) {
                    $('.recurringPaymentDay').css('visibility','visible');
                } else {
                    $('.recurringPaymentDay').css('visibility','hidden');
                }
            });

            $('.btn-submit').click(function () {
                var form1 = $('#processPaymentForm');
                if ($("#financeAccount").val() != '_new') {
                    $(".preloader").fadeIn("slow");
                    form1.submit();
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
        });

        function ajaxSubmit(form) {
            form.find('.invalid-feedback strong').html('');
            form.find('.error-message').html('');
            $('.form-control').removeClass('is-invalid');
            $('.btn-submit').prop('disabled',true);
            $(".preloader").fadeIn("slow");
            $.ajax({
                url     : form.find('input[name=form-action]').val(),
                type    : 'post',
                data    : form.find(':input').serialize(),
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
                    form.find('.invalid-feedback strong').html('');
                    form.find('.error-message').html('');
                    $('.form-control').removeClass('is-invalid');
                    if(json.status === 422) {
                        var errors = json.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('.'+key+'-error').closest('div').find('.form-control').addClass('is-invalid');
                            $('.'+key+'-error').html(value);
                        });
                    } else {
                        form.find('.invalid-feedback strong').html('');
                        $('.form-control').removeClass('is-invalid');
                        form.find('.error-message').html(json.responseJSON.message);
                    }
                }
            });
        }

        function ajaxSubmitConnectedForm(form) {
            $(form).find('.invalid-feedback strong').html('');
            $(form).find('.error-message').html('');
            $(".preloader").fadeIn("slow");
            $.ajax({
                url     : $(form).find('input[name=form-action]').val(),
                type    : $(form).attr('method'),
                data    : $(form).serialize(),
                dataType: 'json',
                success : function (json) {
                    $(".preloader").fadeOut("slow");
                    var finance_id = json.finance_id;
                    $('input[name=newFinanceAccount]').val(finance_id);
                    $('.finance-form').submit();
                },
                error: function(json) {
                    $(".preloader").fadeOut("slow");
                    $(form).find('.error-message').html(json.responseJSON.message);
                }
            });
        }

    </script>
    <script>
        // Client validation required fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                        $("[required]:invalid").each(function(){
                            var feedback_element = $(this).next(".invalid-feedback");
                            feedback_element.text(feedback_element.data("fieldname") ? "Field " + feedback_element.data("fieldname") + " is required" : "This field is required");
                        });
                    }, false);
                });
            }, false);
        })();
    </script>
@endsection
