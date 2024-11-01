<div class="card-header border-top financeSwitchHeader">
    <div class="custom-control custom-radio custom-control-inline">
        <label class="" for="financeSwitch1"><i class="fal fa-credit-card-front"></i> Add Debit, credit or prepaid card</label>
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
    </form>
</div>

@section('scripts_in_modules')
<script>
    $(document).ready(function() {
        $('.financeSwitchContent input').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        if ($('#financeSwitchContent1 .is-invalid').length > 0) {
            $('#financeSwitchContent1').show();
        }
    });
</script>
@endsection
