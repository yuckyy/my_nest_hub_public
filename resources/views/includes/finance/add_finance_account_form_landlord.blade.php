<!-- Landlord -->
<div class="card-header border-top financeSwitchHeader">
    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="financeSwitch1" name="financeSwitch" class="custom-control-input" value="stripe" checked>
        <label class="custom-control-label" for="financeSwitch1"><i class="fab fa-cc-stripe"></i> Connect Stripe Account</label>
    </div>
    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="financeSwitch2" name="financeSwitch" class="custom-control-input" value="paypal">
        <label class="custom-control-label" for="financeSwitch2"><i class="fab fa-paypal"></i> Add PayPal Account</label>
    </div>
    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" id="financeSwitchDwolla" name="financeSwitch" class="custom-control-input" value="dwolla_target">
        <label class="custom-control-label" for="financeSwitchDwolla"><i class="fa fa-envelope-open-dollar"></i> Receive ACH Payments</label>
    </div>
</div>
<div class="financeSwitchContent" id="financeSwitchContent1">
    <form method="post" action="{{ route('send-stripe-connect') }}">
        <div class="card-body">
            @if (session('error'))
                <div class="is-invalid">
                    <span class="invalid-feedback" role="alert" style="display:block;">
                        <strong>{{ session('error') }}</strong>
                    </span>
                </div>
            @endif
                @csrf
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="holderName">Holder Name <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" name="account_holder_name" id="holderName" value="{{ old('account_holder_name') }}" maxlength="64">
                        @error('account_holder_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stripeAccount">Stripe Account ID <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('stripe_account_id') is-invalid @enderror" name="stripe_account_id" id="stripeAccount" value="{{ old('stripe_account_id') }}" maxlength="64">
                        @error('stripe_account_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3 pt-1">
                        <img class="d-block mt-md-4" src="{{ url('/') }}/images/Powered-by-Stripe-blurple.png" height="30" alt="Powered by Stripe">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-8 mb-3">
                        <label for="financeAccountNickname">Financial Account Nickname <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('nickname') is-invalid @enderror" name="nickname" id="financeAccountNickname" value="{{ old('nickname') }}" maxlength="32">
                        @error('nickname')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
        </div>

        <div class="card-footer text-muted">
            <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm float-right"><i class="fal fa-check-circle mr-1"></i> Save Financial Account</button>
        </div>
    </form>

</div>

<div class="financeSwitchContent" id="financeSwitchContent2" style="display: none">
    <form method="post" action="{{ route('add-paypal-account') }}">
        @csrf
        <div class="card-body">
            @if (session('error'))
                <div class="is-invalid">
                    <span class="invalid-feedback" role="alert" style="display:block;">
                        <strong>{{ session('error') }}</strong>
                    </span>
                </div>
            @endif
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="financeAccountPayPalEmail">PayPal Email <i class="required fal fa-asterisk"></i></label>
                    <input type="text" class="form-control @error('paypal_email') is-invalid @enderror" name="paypal_email" id="financeAccountPayPalEmail" value="{{ old('paypal_email') }}" maxlength="64">
                    <span class="invalid-feedback" role="alert">
                        Please enter a valid email address
                        @error('paypal_email')
                            {{ $message }}
                        @enderror
                    </span>
                </div>
                <div class="col-md-4 mb-3">
                    <img class="d-block mt-4 rounded" src="{{ url('/') }}/images/paypal.png" alt="PayPal" height="38" >
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm float-right"><i class="fal fa-check-circle mr-1"></i> Save Financial Account</button>
        </div>
    </form>

</div>

<div class="financeSwitchContent" id="financeSwitchContentDwolla" style="display: none">
    {{--}}
    @if(!empty($identity))
    {{--}}

    <div class="card-body">
        <div class="h3 text-warning">Under Construction</div>
    </div>
    {{--}}
        <!-- TODO Archive DWOLLA integration (not remove) -->

        <form method="post" action="{{ route('add-dwolla-ach-landlord-account') }}">
            @csrf
            <div class="card-body">
                @if (session('dwolla-error'))
                    <div class="customFormAlert alert alert-danger" role="alert">
                        {!! session('dwolla-error') !!}
                    </div>
                @endif

                <div class="inRowComment text-primary2">
                    <div><i class="fas fa-exclamation-circle text-primary2"></i> Please ensure that Holder name is spelled exactly as it appears on your banking information.</div>
                    <div class="pl-3">FREE for the landlord. Your tenant will pay a maximum $5.00 per transaction.</div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="holderName">Holder Name <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('dwolla_account_holder_name') is-invalid @enderror" name="dwolla_account_holder_name" id="holderName" value="{{ old('dwolla_account_holder_name') ?? (!empty($identity) ? ($identity->first_name . " " . $identity->last_name) : '') }}" maxlength="64">
                        @error('dwolla_account_holder_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="dwolla_account_holder_name-error"></strong>
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <div class="d-md-flex">
                            <label for="routingNumber" class="d-inline d-md-flex">Routing Number <i class="required fal fa-asterisk"></i></label>
                            <span id="achTooltip1" class="ml-2 ml-md-auto routingNumberTooltip" data-trigger="hover"  data-toggle="tooltip" data-html="true" data-container="#achTooltip1" title="<img src='/images/check-graphic.jpg' width='480' height='88'>">
                                <i class="fas fa-question-circle text-secondary"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control @error('dwolla_routing_number') is-invalid @enderror" name="dwolla_routing_number" id="routingNumber" value="{{ old('dwolla_routing_number') }}" data-type="integer" maxlength="9">
                        @error('dwolla_routing_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="dwolla_routing_number-error"></strong>
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-md-flex">
                            <label class="d-inline d-md-flex" for="accountNumber">Account Number <i class="required fal fa-asterisk"></i></label>
                            <span id="achTooltip2" class="ml-2 ml-md-auto routingNumberTooltip">
                                <i class="fas fa-question-circle text-secondary" data-trigger="hover" data-toggle="tooltip" data-html="true" data-container="#achTooltip2" title="<img src='/images/check-graphic.jpg' width='480' height='88'>"></i>
                            </span>
                        </div>

                        <input type="text" class="form-control @error('dwolla_account_number') is-invalid @enderror" name="dwolla_account_number" id="accountNumber" value="{{ old('dwolla_account_number') }}" data-type="integer" maxlength="17">
                        @error('dwolla_account_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="dwolla_account_number-error"></strong>
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="bankAccountType">Bank Account Type <i class="required fal fa-asterisk"></i></label>
                        <select class="form-control @error('bank_account_type') is-invalid @enderror" name="dwolla_bank_account_type" id="bankAccountType">
                            <option value="checking" @if(old('dwolla_bank_account_type') == 'checking') selected @endif >Checking</option>
                            <option value="savings" @if(old('dwolla_bank_account_type') == 'savings') selected @endif >Savings</option>
                        </select>
                        @error('dwolla_bank_account_type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="dwolla_bank_account_type_confirmation-error"></strong>
                        </span>
                    </div>
                </div>

                @if(empty(Auth::user()->dwolla_tos))
                <div>
                    <div class="custom-control custom-checkbox custom-control-inline pr-4 primary-border-checkbox">
                        <input
                                type="checkbox"
                                class="custom-control-input @error('accept_tos') is-invalid @enderror"
                                name="accept_tos"
                                value="1"
                                id="accept_tos"
                                @if(old('accept_tos') == '1') checked @endif
                        >
                        <label
                                class="custom-control-label d-block"
                                for="accept_tos"
                        >
                            By checking this box you agree to our partner <a target="_blank" href="https://www.dwolla.com/legal/tos/" class="text-primary2">Dwolla's Terms of Service</a> and <a target="_blank" href="https://www.dwolla.com/legal/privacy/" class="text-primary2">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer text-muted">
                <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm float-right">
                    <i class="fal fa-check-circle mr-1"></i>
                    @if(empty(Auth::user()->dwolla_tos))
                        Agree and Save
                    @else
                        Save Financial Account
                    @endif
                </button>
            </div>
        </form>
    {{--}}

    {{--}}
    @else
        <div class="card-body">
            <div class="alert alert-warning mb-0" role="alert">
                <p>You will be eligible to use this feature after the successful user verification.</p>
                <div>
                    <a class="btn btn-sm btn-primary" href="{{route("profile/identity")}}"><i class="fal fa-shield-alt mr-1"></i> Process User Verification</a>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
        </div>
    @endif
    {{--}}

</div>
@section('scripts_in_modules')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if(window.location.hash) {
            $('#addFinanceAccountContent').removeClass('collapse');
            var activateElement;
            switch (window.location.hash) {
                case '#stripe':
                    activateElement = $('input[type="radio"][value="stripe"]').first();
                    break;
                case '#paypal':
                    activateElement = $('input[type="radio"][value="paypal"]').first();
                    break;
                case '#ach':
                    activateElement = $('input[type="radio"][value="dwolla_target"]').first();
                    break;
            }
            activateElement.prop('checked',true);
            switchAccountForm(activateElement.val());
        } else {
            $('input[type="radio"][name="financeSwitch"]').first().prop('checked',true);
        }

        $('.financeSwitchContent input').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        if ($('#financeSwitchContentDwolla .is-invalid, #financeSwitchContentDwolla .customFormAlert').length > 0) {
            $('#financeSwitchContent1').hide();
            $('#financeSwitchContent2').hide();
            $('#financeSwitchContentDwolla').show();
            $('input[type="radio"][value="dwolla_target"]').prop('checked',true);
        }
        $('input[type=radio][name=financeSwitch]').change(function () {
            switchAccountForm(this.value);
        });
    });
    function switchAccountForm(accountType){
        if (accountType == 'stripe') {
            $('#financeSwitchContent1').show();
            $('#financeSwitchContent2').hide();
            $('#financeSwitchContentDwolla').hide();
            window.location.hash = 'stripe';
        } else if (accountType == 'paypal') {
            $('#financeSwitchContent1').hide();
            $('#financeSwitchContent2').show();
            $('#financeSwitchContentDwolla').hide();
            window.location.hash = 'paypal';
        } else if (accountType == 'dwolla_target') {
            $('#financeSwitchContent1').hide();
            $('#financeSwitchContent2').hide();
            $('#financeSwitchContentDwolla').show();
            window.location.hash = 'ach';
        }
    }
</script>
@endsection
