<input type="hidden" name="record_id" value="{{ $finance->id }}">
<div class="border-bottom pb-1">
    <h6 class="linkedUnitsHeader pb-0">
        <span class="paymentIcon"><i class="{{ financialTypeIconClassSmall($finance->finance_type) }}"></i></span>
        <strong class="paymentNickname">{{ $finance->nickname }}</strong>
    </h6>
</div>
<div class="unitsList">
    @if (count($units) > 0)
        @foreach ($units as $u)
            <div class="row no-gutters pt-3">
                <div class="col-sm-8 text-left">{{ $u->property->full_address }}
                    @if (Auth::user()->isTenant() && count($u->property->user->financialStripeAccounts()) == 0)
                        <br><small class="text-danger">Your landlord didn't setup financial account yet.</small>
                    @endif
                </div>
                <div class="col-sm-4 text-right">
                    <label for="linked-{{ $u->id }}">{{ $u->name }}</label>
                    <input
                        type="checkbox"
                        name="linked_id[]"
                        value="{{ $u->id }}"
                        id="linked-{{ $u->id }}"
                        {{ $finance->isLinked($u->id) ? 'checked' : '' }}
                        {{ Auth::user()->isTenant() && count($u->property->user->financialStripeAccounts()) == 0 ? 'disabled' : '' }}
                    >
                </div>
            </div>
        @endforeach
    @elseif (Auth::user()->isTenant())
        <p>There is no active lease in our account.</p>
    @endif
</div>
@error('linked_id')
    <div class="is-invalid">
        <span class="invalid-feedback" role="alert" style="display:block;">
            <strong>{{ $message }}</strong>
        </span>
    </div>
@enderror
