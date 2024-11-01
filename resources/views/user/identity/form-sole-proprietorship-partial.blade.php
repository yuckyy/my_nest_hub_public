<div class="card-body bg-light border-top identityBarCell">
    <div class="identityBarItem">
        <div class="identityBarTitle">Business Owner</div>
        <div class="identityBarIcon"><i class="fal fa-passport"></i></div>
    </div>

    <div class="form-row">
        <div class="col-md-4 mb-3">
            <label for="first_name">First Name <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" id="first_name" value="{{ old('first_name') ?? $identity->first_name ?? $user->name }}" maxlength="64">
            @error('first_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="last_name">Last Name <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" id="last_name" value="{{ old('last_name') ?? $identity->last_name ?? $user->lastname }}" maxlength="64">
            @error('last_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="email">Email <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') ?? $identity->email ?? $user->email }}" maxlength="64" readonly>
            <span class="invalid-feedback" role="alert">
                Please enter a valid email address
                @error('email')
                {{ $message }}
                @enderror
            </span>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-3 mb-3">
            <label for="dob">
                DOB <i class="required fal fa-asterisk"></i>
            </label>
            <input
                    type="date"
                    class="form-control @error('dob') is-invalid @enderror"
                    id="dob"
                    name="dob"
                    value="{{ old('dob') ?? (($identity && $identity->dob) ? \Carbon\Carbon::parse($identity->dob)->format("Y-m-d") : \Carbon\Carbon::now()->subYears(18)->format("Y-m-d")) }}"
                    max="{{ \Carbon\Carbon::now()->subYears(18)->format("Y-m-d") }}"
                    min="1900-01-01"
                    required="required">
            <span class="invalid-feedback" role="alert">
                @error('dob')
                {{ $message }}
                @enderror
            </span>
        </div>
        <div class="col-md-3 mb-3">
            <label for="ssn">last 4 digits of SSN <i class="required fal fa-asterisk"></i></label>
            <input name="ssn" type="text" class="form-control @error('ssn') is-invalid @enderror" id="ssn" placeholder="" value="{{ old('ssn') ?? $identity->ssn ?? '' }}" maxlength="4" data-mask="0000">
            @error('ssn')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <span class="invalid-feedback" role="alert">
                <strong class="ssn-error"></strong>
            </span>
        </div>
    </div>

</div>
<div class="card-body bg-light border-top identityBarCell">
    <div class="identityBarItem">
        <div class="identityBarTitle">Business</div>
        <div class="identityBarIcon"><i class="fal fa-industry-alt"></i></div>
    </div>

    <div class="form-row">
        <div class="col-md-4 mb-3">
            <label for="business_name">Business Name <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control @error('business_name') is-invalid @enderror" name="business_name" id="business_name" value="{{ old('business_name') ?? $identity->business_name ?? '' }}" maxlength="255">
            @error('business_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <span class="invalid-feedback" role="alert">
                <strong class="business_name-error"></strong>
            </span>
        </div>
        <div class="col-md-4 mb-3">
            <label for="business_classification">Business Classification <i class="required fal fa-asterisk"></i></label>
            <select class="form-control @error('business_classification') is-invalid @enderror" name="business_classification" id="business_classification">
                <option value="">Please Select</option>
                @foreach($classifications as $class1)
                    <optgroup label="{{ $class1->name }}">
                        @foreach($class1->_embedded->{'industry-classifications'} as $c)
                            <option value="{{ $c->id }}" @if((old('business_classification') ?? $identity->business_classification ?? '') == $c->id) selected @endif>{{ $c->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('business_classification')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <span class="invalid-feedback" role="alert">
                <strong class="address-error"></strong>
            </span>
        </div>
        <div class="col-md-4 mb-3">
            <label for="ein">Employer Identification Number</label>
            <input type="text" class="form-control @error('ein') is-invalid @enderror" name="ein" id="ein" value="{{ old('ein') ?? $identity->ein ?? ''}}" maxlength="9">
            @error('ein')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <span class="invalid-feedback" role="alert">
                <strong class="ein-error"></strong>
            </span>
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-6 mb-3">
            <label for="address">Address <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" value="{{ old('address') ?? $identity->address ?? '' }}" maxlength="255">
            @error('address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <span class="invalid-feedback" role="alert">
                <strong class="address-error"></strong>
            </span>
        </div>
        <div class="col-md-6 mb-3">
            <label for="address_2">Address Second Line</label>
            <input type="text" class="form-control @error('address_2') is-invalid @enderror" name="address_2" id="address_2" value="{{ old('address_2') ?? $identity->address_2 ?? '' }}" maxlength="255">
            @error('address_2')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <span class="invalid-feedback" role="alert">
                <strong class="address_2-error"></strong>
            </span>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6 mb-3">
            <label for="city">City <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') ?? $identity->city ?? '' }}" maxlength="64">
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
            <label for="state">State <i class="required fal fa-asterisk"></i></label>
            <select name="state" class="custom-select form-control @error('state') is-invalid @enderror" id="state">
                <option value="">-</option>
                @foreach ($states as $s)
                    <option value="{{ $s->code }}" {{ ( old('state') ?? $identity->state ?? '' ) == $s->code ? 'selected' : '' }}>{{ $s->name }}</option>
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
            <label for="zip">Zip Code <i class="required fal fa-asterisk"></i></label>
            <input name="zip" maxlength="5" type="text" class="form-control @error('zip') is-invalid @enderror" id="zip" placeholder="" value="{{ old('zip') ?? $identity->zip ?? '' }}" maxlength="32">
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
</div>
