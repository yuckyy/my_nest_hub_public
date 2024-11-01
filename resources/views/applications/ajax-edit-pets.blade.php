@foreach ($pets ?? [] as $key => $value)
    <div class="addRowBox savedRow">
        <div class="form-row">
            <div class="col-md-5 mb-3">
                <label for="petType{{$key+1}}">Type <i class="required fal fa-asterisk"></i></label>
                <select name="pets[{{$key+1}}][pets_type_id]" class="custom-select fixedMaxInputWidth" id="petType{{$key+1}}">
                    @foreach($petsTypes as $petsType)
                        <option @if($petsType->id == $value->pet_type_id) selected="selected" @endif value="{{ $petsType->id }}">{{ $petsType->name }}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback" role="alert"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="petDescription{{$key+1}}">Description <i class="required fal fa-asterisk"></i></label>
                <input type="text" value="{{ $value->description }}" class="form-control" name="pets[{{$key+1}}][description]" id="petDescription{{$key+1}}">
                <span class="invalid-feedback" role="alert"></span>
            </div>

            <div class="col-md-1 mb-3 removeFormRowCell">
                <a href="#">remove <i class="fal fa-times"></i></a>
            </div>
        </div>
    </div>
@endforeach

<div id="addEmpItemsBox" class="addRowBox">
    <div class="form-row rowTemplate">
        <div class="col-md-5 mb-3">
            <label for="petType0">Type <i class="required fal fa-asterisk"></i></label>
            <select name="pets[0][pets_type_id]" class="custom-select fixedMaxInputWidth" id="petType0" disabled>
                @foreach($petsTypes as $petsType)
                    <option value="{{ $petsType->id }}">{{ $petsType->name }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert"></span>
        </div>
        <div class="col-md-6 mb-3">
            <label for="petDescription0">Description <i class="required fal fa-asterisk"></i></label>
            <input type="text" class="form-control" name="pets[0][description]" id="petDescription0" disabled>
            <span class="invalid-feedback" role="alert"></span>
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
        <i class="fal fa-plus-circle mr-1"></i> add pet
    </button>
</div>
<input type="hidden" name="petsUpdate" value="1">
