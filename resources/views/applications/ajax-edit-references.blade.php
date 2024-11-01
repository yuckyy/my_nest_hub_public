@foreach ($references ?? [] as $key => $value)
    <div class="addRowBox savedRow">
        <div class="form-row">

            <div class="col-md-4 mb-3">
                <label for="referenceName{{$key+1}}">Name</label>
                <input type="text" value="{{ $value->name }}" class="form-control" name="references[{{$key+1}}][name]" id="referenceName{{$key+1}}">
                <span class="invalid-feedback" role="alert"></span>
            </div>
            <div class="col-md-4 mb-3">
                <label for="referenceEmail{{$key+1}}">Email</label>
                <input type="email" pattern="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" value="{{ $value->email }}" class="form-control" name="references[{{$key+1}}][email]" id="referenceEmail{{$key+1}}">
                <span class="invalid-feedback" role="alert"></span>
            </div>
            <div class="col-md-3 mb-3">
                <label for="referencePhone{{$key+1}}">Phone</label>
                <input type="text" value="{{ $value->phone }}" data-mask="000-000-0000" class="form-control" name="references[{{$key+1}}][phone]" data-type="phone" id="referencePhone{{$key+1}}">
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
        <div class="col-md-4 mb-3">
            <label for="referenceName0">Name</label>
            <input type="text" class="form-control" name="references[0][name]" id="referenceName0" disabled >
            <span class="invalid-feedback" role="alert"></span>
        </div>
        <div class="col-md-4 mb-3">
            <label for="referenceEmail0">Email</label>
            <input type="email" pattern="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" class="form-control" name="references[0][email]" id="referenceEmail0" disabled >
            <span class="invalid-feedback" role="alert"></span>
        </div>
        <div class="col-md-3 mb-3">
            <label for="referencePhone0">Phone</label>
            <input type="text" data-mask="000-000-0000" class="form-control" name="references[0][phone]" data-type="phone" id="referencePhone0" disabled >
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
        <i class="fal fa-plus-circle mr-1"></i> add reference
    </button>
</div>
<input type="hidden" name="referencesUpdate" value="1">
