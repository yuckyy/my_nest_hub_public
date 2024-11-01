@foreach ($additionalIncomes ?? [] as $key => $value)
    <div class="addRowBox savedRow">
        <div class="form-row">
            <div class="col-md-8 mb-3">
                <label for="additionalIncomesDescription{{$key+1}}">Description</label>

                <input
                        type="text"
                        class="form-control"
                        name="additionalIncomes[{{$key+1}}][description]"
                        value="{{ $value->description }}"
                        maxlength="255"
                        id="additionalIncomesDescription{{$key+1}}"
                >
                <span class="invalid-feedback" role="alert">
                </span>
            </div>

            <div class="col-md-3 mb-3">
                <label for="additionalIncomesAmount{{$key+1}}">Amount <i class="required fal fa-asterisk"></i></label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">$</div>
                    </div>

                    <input
                            type="text"
                            class="form-control"
                            name="additionalIncomes[{{$key+1}}][amount]"
                            value="{{ $value->amount }}"
                            data-type="currency"
                            maxlength="12"
                            data-maxamount="9999999"
                            id="additionalIncomesAmount{{$key+1}}"
                    >
                    <span class="invalid-feedback" role="alert">
                    </span>
                </div>
            </div>

            <div class="col-md-1 mb-3 removeFormRowCell">
                <a href="#">remove <i class="fal fa-times"></i></a>
            </div>
        </div>
    </div>
@endforeach

<div id="addEmpItemsBox" class="addRowBox">
    <div class="form-row rowTemplate">
        <div class="col-md-6 col-lg-8 mb-3">
            <label for="addAdditionalIncomesDescription0">Description</label>
            <input type="text" class="form-control" name="additionalIncomes[0][description]" id="addAdditionalIncomesDescription0" disabled required>
            <span class="invalid-feedback" role="alert">
            </span>
        </div>
        <div class="col-md-4 col-lg-3 mb-3">
            <label for="addAdditionalIncomesAmount0">Amount <i class="required fal fa-asterisk"></i></label>

            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">$</div>
                </div>
                <input type="text"
                        data-type="currency"
                        maxlength="12"
                        data-maxamount="9999999"
                        class="form-control"
                        name="additionalIncomes[0][amount]"
                        required="required"
                        id="addAdditionalIncomesAmount0"
                        disabled >
                <span class="invalid-feedback" role="alert">
                </span>
            </div>
        </div>
        <div class="col-md-2 col-lg-1 mb-3 removeFormRowCell">
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
        <i class="fal fa-plus-circle mr-1"></i> add income
    </button>
</div>
<input type="hidden" name="additionalIncomesUpdate" value="1">
