<!-- Tenant -->
@if(!empty($lease) && $lease->landlordLinkedFinanceStripe())
    <div class="card-header border-top financeSwitchHeader">
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="financeSwitch1" name="financeSwitch" class="custom-control-input" value="card" checked>
            <label class="custom-control-label" for="financeSwitch1"><i class="fal fa-credit-card-front dn2px"></i> Add Debit, credit or prepaid card</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="financeSwitch2" name="financeSwitch" class="custom-control-input" value="bank">
            <label class="custom-control-label" for="financeSwitch2"><i class="fal fa-money-check-edit-alt up2px mr-2"></i> Add Checking account</label>
        </div>
    </div>
    <div class="financeSwitchContent" id="financeSwitchContent1">
        @if (session('card_error'))
            <div class="is-invalid">
                <span class="invalid-feedback" role="alert" style="display:block;">
                    <strong>{{ session('card_error') }}</strong>
                </span>
            </div>
        @endif
        <form method="post">
            @csrf
            <input type="hidden" name="form-action" value="{{ route('add-card-account') }}">
            <div class="card-body">
                <div class="form-row">
                    <span class="col-md-12 invalid-feedback" role="alert" style="display:block;">
                        <strong class="error-message"></strong>
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3 text-center pt-3">
                        <img class="mb-1" src="{{ url('/') }}/images/visa.svg" width="40%" alt="visa">
                        <img class="mb-1" src="{{ url('/') }}/images/mastercard.svg" width="40%" alt="mastercard">
                        <img class="mb-1" src="{{ url('/') }}/images/amex.svg" width="40%" alt="amex">
                        <img class="mb-1" src="{{ url('/') }}/images/discover.svg" width="40%" alt="discover">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="cardNumber">Card Number <i class="required fal fa-asterisk"></i></label>
                                <input type="text" class="form-control @error('cardNumber') is-invalid @enderror" name="cardNumber" id="cardNumber" value="{{ old('cardNumber') }}" data-type="integer" maxlength="19">
                                @error('cardNumber')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <span class="invalid-feedback" role="alert">
                                    <strong class="cardNumber-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="expiration">Expiration Month/Year <i class="required fal fa-asterisk"></i></label>
                                <input type="text" class="form-control @error('expiration') is-invalid @enderror" name="expiration" id="expiration" value="{{ old('expiration') }}" maxlength="5" data-mask="00/00">
                                @error('expiration')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <span class="invalid-feedback" role="alert">
                                    <strong class="expiration-error"></strong>
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv">Security Code <i class="required fal fa-asterisk"></i> <a href="#" data-toggle="modal" data-target="#cardCvvModal" class="inRowComment pb-0 text-capitalize"><i class="fal fa-question-circle"></i> What is it?</a></label>
                                <input type="text" class="form-control @error('cvv') is-invalid @enderror" name="cvv" id="cvv" value="{{ old('cvv') }}" data-type="integer" maxlength="4">
                                @error('cvv')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <span class="invalid-feedback" role="alert">
                                    <strong class="cvv-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="nameOnCard">Name on Card <i class="required fal fa-asterisk"></i></label>
                                <input type="text" class="form-control @error('nameOnCard') is-invalid @enderror" name="nameOnCard" id="nameOnCard" value="{{ old('nameOnCard') }}" maxlength="64">
                                @error('nameOnCard')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <span class="invalid-feedback" role="alert">
                                    <strong class="nameOnCard-error"></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="billingAddress">Billing Address <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('billingAddress') is-invalid @enderror" name="billingAddress" id="billingAddress" value="{{ old('billingAddress') }}" maxlength="255">
                        @error('billingAddress')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="billingAddress-error"></strong>
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="billingAddress2">Billing Address Second Line</label>
                        <input type="text" class="form-control @error('billingAddress2') is-invalid @enderror" name="billingAddress2" id="billingAddress2" value="{{ old('billingAddress2') }}" maxlength="255">
                        @error('billingAddress2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="billingAddress2-error"></strong>
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="validationCustom03">City <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="validationCustom03" name="city" value="{{ old('city') }}" maxlength="64">
                        @error('city')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="city-error"></strong>
                        </span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom04">State <i class="required fal fa-asterisk"></i></label>
                        <select name="state" class="custom-select form-control @error('state') is-invalid @enderror" id="validationCustom04">
                            <option value="">-</option>
                            @foreach ($states as $s)
                            <option value="{{ $s->code }}" {{ old('state') == $s->code ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('state')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="state-error"></strong>
                        </span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="validationCustom05">Zip Code <i class="required fal fa-asterisk"></i></label>
                        <input name="zip" maxlength="5" type="text" class="form-control @error('zip') is-invalid @enderror" id="validationCustom05" placeholder="" value="{{ old('zip') }}" maxlength="32">
                        @error('zip')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="zip-error"></strong>
                        </span>
                    </div>
                </div>
                <div class="form-row justify-content-md-center">
                    <div class="col-md-6 mb-3">
                        <label for="financeAccountNickname1">Financial Account Nickname <i class="required fal fa-asterisk"></i></label>
                        <input type="text" class="form-control @error('financeAccountNickname') is-invalid @enderror" name="financeAccountNickname" id="financeAccountNickname1" value="{{ old('financeAccountNickname') }}" maxlength="64">
                        @error('financeAccountNickname')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <span class="invalid-feedback" role="alert">
                            <strong class="financeAccountNickname-error"></strong>
                        </span>
                    </div>
                </div>
            </div>
            @if (!isset($newLease))
                <div class="card-footer text-muted">
                    <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                    <button type="button" onclick="ajaxSubmit(this.form)" class="btn btn-primary btn-sm float-right btn-submit"><i class="fal fa-check-circle mr-1"></i> Save Financial Account</button>
                </div>
            @endif
        </form>
    </div>

    <div class="financeSwitchContent" id="financeSwitchContent2" style="display: none">
        @if (session('ach_error'))
            <div class="is-invalid">
                <span class="invalid-feedback" role="alert" style="display:block;">
                    <strong>{{ session('ach_error') }}</strong>
                </span>
            </div>
        @endif
        <form method="post" action="{{ route('add-checking-account') }}" id="addCheckingAccount">
            @csrf
            <input type="hidden" name="form-action" value="{{ route('add-checking-account') }}">
            <div class="card-body">
                <div class="form-row">
                    <span class="col-md-12 invalid-feedback" role="alert" style="display:block;">
                        <strong class="error-message"></strong>
                    </span>
                </div>
                <div class="text-center">
                    <div class="inRowComment"><i class="fal fa-info-circle"></i> Connect your bank account here.</div>
                </div>
                <div class="pb-2 text-center">
                    <button type="button" class="btn btn-lg btn-primary" id="plaidConnect"><i class="fal fa-plug mr-2"></i> Connect Your Bank</button>
                </div>
            </div>
            <input type="hidden" name="plaid_public_token" id="plaid_public_token">
            <input type="hidden" name="plaid_account_id" id="plaid_account_id">

        @if (!isset($newLease))
                <div class="card-footer text-muted">
                    <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                    {{--}}<button type="button" onclick="ajaxSubmit(this.form)" class="btn btn-primary btn-sm float-right btn-submit"><i class="fal fa-check-circle mr-1"></i> Save Financial Account</button>{{--}}
                </div>
            @endif
        </form>
    </div>
@elseif(!empty($lease) && $lease->landlordLinkedFinanceDwolla())

    <!-- TODO Archive DWOLLA integration (not remove) -->

    <div class="financeSwitchContent border-top">
        @if (session('ach_error'))
            <div class="is-invalid">
                <span class="invalid-feedback" role="alert" style="display:block;">
                    <strong>{{ session('ach_error') }}</strong>
                </span>
            </div>
        @endif
        <form method="post" action="{{ route('add-dwolla-account') }}" id="addDwollaAccount">
            @csrf

            <input type="hidden" name="financeSwitch" value="dwolla_source">

            <input type="hidden" name="form-action" value="{{ route('add-dwolla-account') }}">
            <div class="card-body">
                <div class="form-row">
                    <span class="col-md-12 invalid-feedback" role="alert" style="display:block;">
                        <strong class="error-message"></strong>
                    </span>
                </div>
                <div id="iavCta">
                    <div class="text-center">
                        <div class="inRowComment"><i class="fal fa-info-circle"></i> Your landlord receives low-fee ACH payments. Please connect your bank account below.</div>
                    </div>

                    <div class="pb-3 text-center">
                        <div class="custom-control custom-checkbox custom-control-inline pr-4 primary-border-checkbox">
                            <input
                                    type="checkbox"
                                    class="custom-control-input" {{--}}@error('accept_tos') is-invalid @enderror{{--}}
                                    name="accept_tos"
                                    value="1"
                                    id="accept_tos"
                            >
                            <label
                                    class="custom-control-label d-block"
                                    for="accept_tos"
                            >
                                By checking this box you agree to our partner <a target="_blank" href="https://www.dwolla.com/legal/tos/" class="text-primary2">Dwolla's Terms of Service</a> and <a target="_blank" href="https://www.dwolla.com/legal/privacy/" class="text-primary2">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <div class="pb-2 text-center">
                        <button type="button" disabled class="btn btn-lg btn-primary" id="dwollaConnect"><i class="fal fa-plug mr-2"></i>Agree and Connect Your Bank</button>
                    </div>
                </div>
                <div id="iavContainer" class="bg-white"></div>
            </div>
            @if (!isset($newLease))
                <div class="card-footer text-muted">
                    <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true" aria-controls="addFinanceAccountContent" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                </div>
            @endif
            <input type="hidden" name="funding_source_url" id="funding_source_url">
        </form>
    </div>
@endif
@section('scripts_in_modules')
@if(!empty($lease) && $lease->landlordLinkedFinanceStripe())
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script type="text/javascript">
    var linkHandler;
    var linkToken;
    function plaidInit(){
        $.ajax({
            url     : '{{ route("plaid-link-token") }}',
            type    : 'post',
            data    : "",
            dataType: 'json',
            success : function (json) {
                linkToken = json.link_token;
                const configs = {
                    token: linkToken,
                    onLoad: function() {
                        // The Link module finished loading.
                    },
                    onSuccess: function(public_token, metadata) {
                        $('#plaid_public_token').val(public_token);
                        $('#plaid_account_id').val(metadata.accounts[0].id);
                        ajaxSubmitConnectedForm('#addCheckingAccount');
                    },
                    onExit: async function(err, metadata) {
                        // The user exited the Link flow.
                        if (err != null) {
                            // The user encountered a Plaid API error
                            // prior to exiting.
                        }
                        // metadata contains information about the institution
                        // that the user selected and the most recent
                        // API request IDs.
                        // Storing this information can be helpful for support.
                    },
                };
                linkHandler = Plaid.create(configs);
            },
            error: function(json) {
            }
        });
    }
</script>
<script>
    $(document).ready(function() {
        $('#plaidConnect').on('click', function(e) {
            linkHandler.open();
        });

        plaidInit();
    });
</script>
@endif
@if(!empty($lease) && $lease->landlordLinkedFinanceDwolla())
<script src="https://cdn.dwolla.com/1/dwolla.js"></script>
<script>
    var iavToken;
    var funding_source_url;
    $(document).ready(function() {
        $('#dwollaConnect').on('click', function(e) {
            console.log('dwollaConnect: Click');
            funding_source_url = false;
            $(".preloader").fadeIn("fast");
            $("#iavCta").hide();
            $.ajax({
                url     : '{{ route("dwolla-iav-token") }}',
                type    : 'post',
                data    : "",
                dataType: 'json',
                success : function (json) {
                    console.log('dwollaConnect: IAV token is ready');
                    $(".preloader").fadeOut("slow");
                    $("#iavContainer").addClass("border");
                    iavToken = json.iav_token;

                    @if(env('APP_ENV') != 'production')
                    dwolla.configure('sandbox');
                    @endif

                    dwolla.iav.start(iavToken, {
                        container: 'iavContainer',
                        stylesheets: [
                            'https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext'
                        ],
                        microDeposits: false,
                        fallbackToMicroDeposits: false
                    }, function(err, res) {
                        console.log('dwollaConnect: IAV is done');
                        if(funding_source_url === false){
                            funding_source_url = res._links['funding-source'].href;
                            $('#funding_source_url').val(funding_source_url);
                            $(".preloader").fadeIn("fast");
                            ajaxSubmitConnectedForm('#addDwollaAccount');
                        }
                    });
                },
                error: function(json) {
                    $(".preloader").fadeOut("slow");
                    console.log(json)
                }
            });

        });
    });
</script>
@endif
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('input[type="radio"][name="financeSwitch"]').first().prop('checked',true);

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
            $('input[type="radio"][value="dwolla"]').prop('checked',true);
        }
        $('input[type=radio][name=financeSwitch]').change(function () {
            if (this.value == 'card') {
                $('#financeSwitchContent2').hide();
                $('#financeSwitchContent1').show();
            } else if (this.value == 'bank') {
                $('#financeSwitchContent1').hide();
                $('#financeSwitchContent2').show();
            }
            switchFeesAmounts(this.value);
            switchCommonFooter(this.value);
        });

        $("#accept_tos").click(function(){
            if($("#accept_tos").is(":checked")){
                $("#dwollaConnect").removeAttr("disabled");
            } else {
                $("#dwollaConnect").attr("disabled", "disabled");
            }
        });
    });
</script>
@endsection
