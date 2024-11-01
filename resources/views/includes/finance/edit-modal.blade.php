<input type="hidden" name="record_id" value="{{ $finance->id }}">
@if ($finance->finance_type != 'paypal')
<div class="h6 border-bottom pb-1">
    <span class="paymentIcon"><i class="fal fa-credit-card"></i></span>
    <strong class="paymentNickname">{{ $finance->nickname }}</strong>
</div>
@endif
@if ($finance->finance_type == 'card')
    <div class="pb-2">
        <label for="edit_billingAddress">Billing Address <i class="required fal fa-asterisk"></i></label>
        <input type="text" class="form-control @error('edit_billingAddress') is-invalid @enderror" name="edit_billingAddress" id="edit_billingAddress" value="{{ old('edit_billingAddress') ? old('edit_billingAddress') : $finance->billing_address }}">
        @error('edit_billingAddress')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="pb-2">
        <label for="edit_billingAddress2">Additional Address</label>
        <input type="text" class="form-control @error('edit_billingAddress2') is-invalid @enderror" name="edit_billingAddress2" id="edit_billingAddress2" value="{{ old('edit_billingAddress2') ? old('edit_billingAddress2') : $finance->billing_address_2 }}">
        @error('edit_billingAddress2')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="pb-2">
        <label for="edit_city">City <i class="required fal fa-asterisk"></i></label>
        <input type="text" class="form-control @error('edit_city') is-invalid @enderror" id="edit_city" name="edit_city" value="{{ old('edit_city') ? old('edit_city') : $finance->city }}">
        @error('edit_city')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="row pb-2">
        <div class="col-md-6">
            <label for="edit_state">State <i class="required fal fa-asterisk"></i></label>
            <select name="edit_state" class="custom-select form-control @error('edit_state') is-invalid @enderror" id="edit_state">
                <option value="">-</option>
                @foreach ($states as $s)
                <option value="{{ $s->code }}" {{ old('edit_state') == $s->code ? 'selected' : $finance->state == $s->code ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            @error('edit_state')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="edit_zip">Zip Code <i class="required fal fa-asterisk"></i></label>
            <input name="edit_zip" maxlength="5" type="text" class="form-control @error('edit_zip') is-invalid @enderror" id="edit_zip" value="{{ old('edit_zip') ? old('edit_zip') : $finance->zip }}">
            @error('edit_zip')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
@endif
@if ($finance->finance_type == 'paypal')
    <div class="row pb-2">
        <div class="col-md-12">
            <label for="paypal_email">PayPal Email <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control" name="paypal_email" id="paypal_email" value="{{ $finance->paypal_email }}">
            <span class="invalid-feedback" role="alert">
                Please enter a valid email address
            </span>
        </div>
    </div>
@else
<div class="row pb-2">
    <div class="col-md-12">
        <label for="edit_nickname">Account Nickname <i class="required fal fa-asterisk"></i></label>
        <input type="text" class="form-control @error('edit_nickname') is-invalid @enderror" name="edit_nickname" id="edit_nickname" value="{{ old('edit_nickname') ? old('edit_nickname') : $finance->nickname }}">
        @error('edit_nickname')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
@endif
