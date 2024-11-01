@foreach ($residenceHistories ?? [] as $key => $value)
    <div class="addRowBox savedRow">
        <div class="form-row">

            <div class="col-md-3 mb-3">
                <label for="residenceDate{{$key+1}}">Date</label>
                <input type="date" value="{{ $value->start_date ? Carbon\Carbon::parse($value->start_date)->format("Y-m-d") : '' }}" name="residenceHistories[{{$key+1}}][start_date]" class="form-control" placeholder="" id="residenceDate{{$key+1}}">
                <span class="invalid-feedback" role="alert">
                </span>
            </div>
            <div class="col-md-3 mb-3">
                <label for="residenceEndDate{{$key+1}}">End Date</label>
                <input type="date" value="{{ $value->end_date ? Carbon\Carbon::parse($value->end_date)->format("Y-m-d") : '' }}" name="residenceHistories[{{$key+1}}][end_date]" class="form-control" placeholder="" id="residenceEndDate{{$key+1}}" >
                <span class="invalid-feedback" role="alert">
                </span>
            </div>
            <div class="col-md-4 mb-3">
                <label>&nbsp;</label>
                <div class="custom-control custom-checkbox pt-2 ml-2">
                    <input type="checkbox" name="residenceHistories[{{$key+1}}][current]" class="custom-control-input endDateCurrent" id="endDateCurrent{{$key+1}}" value="1" @if($value->current) checked="checked" @endif >
                    <span class="invalid-feedback" role="alert">
                    </span>
                    <label class="custom-control-label" for="endDateCurrent{{$key+1}}">Current</label>
                </div>
            </div>
            <div class="clear"></div>
            <div class="col-md-6 mb-3">
                <label for="residenceAddress{{$key+1}}">Address</label>
                <input type="text" value="{{ $value->address }}" class="form-control" name="residenceHistories[{{$key+1}}][address]" id="residenceAddress{{$key+1}}" >
                <span class="invalid-feedback" role="alert">
                </span>
            </div>
            <div class="col-md-3 mb-3">
                <label for="residenceCity{{$key+1}}">City</label>
                <input type="text" value="{{ $value->city }}" class="form-control" name="residenceHistories[{{$key+1}}][city]" id="residenceCity{{$key+1}}" >
                <span class="invalid-feedback" role="alert">
                </span>
            </div>
            <div class="col-md-2 mb-3">
                <label for="residenceState{{$key+1}}">State</label>
                <select name="residenceHistories[{{$key+1}}][state_id]" class="custom-select fixedMaxInputWidth" id="residenceState{{$key+1}}" required >
                    <option value hidden>Select</option>
                    @foreach($states as $state)
                        <option @if($state->id == $value->state_id) selected="selected" @endif value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback" role="alert">
                </span>
            </div>

            <div class="col-md-1 mb-3 removeFormRowCell">
                <a href="#">remove <i class="fal fa-times"></i></a>
            </div>
        </div>
    </div>
@endforeach

<div id="addEmpItemsBox" class="addRowBox">
    <div class="form-row rowTemplate">
        <div class="col-md-3 mb-3">
            <label for="residenceDate0">Date</label>
            <input type="date" value="null" name="residenceHistories[0][start_date]" class="form-control" placeholder="" id="residenceDate0" disabled >
            <span class="invalid-feedback" role="alert">
            </span>
        </div>
        <div class="col-md-3 mb-3">
            <label for="residenceEndDate0">End Date</label>
            <input type="date" value="null" name="residenceHistories[0][end_date]" class="form-control" placeholder="" id="residenceEndDate0" disabled >
            <span class="invalid-feedback" role="alert">
            </span>
        </div>
        <div class="col-md-4 mb-3">
            <label>&nbsp;</label>
            <div class="custom-control custom-checkbox pt-2 ml-2">
                <input type="checkbox" name="residenceHistories[0][current]" class="custom-control-input endDateCurrent" id="endDateCurrent0" value="1" disabled >
                <span class="invalid-feedback" role="alert">
                </span>
                <label class="custom-control-label" for="endDateCurrent0">Current</label>
            </div>
        </div>
        <div class="clear"></div>
        <div class="col-md-6 mb-3">
            <label for="residenceAddress0">Address</label>
            <input type="text" class="form-control" name="residenceHistories[0][address]" id="residenceAddress0" disabled >
            <span class="invalid-feedback" role="alert">
            </span>
        </div>
        <div class="col-md-3 mb-3">
            <label for="residenceCity0">City</label>
            <input type="text" class="form-control" name="residenceHistories[0][city]" id="residenceCity0" disabled >
            <span class="invalid-feedback" role="alert">
            </span>
        </div>
        <div class="col-md-2 mb-3">
            <label for="residenceState0">State</label>
            <select name="residenceHistories[0][state_id]" class="custom-select fixedMaxInputWidth" id="residenceState0" required disabled >
                <option value hidden>Select</option>
                @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert">
            </span>
        </div>

        <div class="col-md-1 mb-3 removeFormRowCell">
            <a href="#">remove <i class="fal fa-times"></i></a>
        </div>
    </div>
</div>

<div class="addBillBox">
    <button
            id="addEmpButton"
            data-n="{{ isset($key) ? $key+2 : 1 }}"
            data-target="addEmpItemsBox"
            class="addRowButton btn btn-outline-secondary btn-sm"
    >
        <i class="fal fa-plus-circle mr-1"></i> add address
    </button>
</div>
<input type="hidden" name="residenceHistoriesUpdate" value="1">
