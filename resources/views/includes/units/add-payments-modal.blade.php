<input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
<input type="hidden" name="bill_amount" value="{{ $invoice->bill_amount }}">
<div class="card text-white bg-primary mb-3">
    <div class="card-header"><i class="fal fa-bell"></i>&nbsp;&nbsp; When to use manual edit?</div>
    <div class="card-body">
        <p class="card-text">
            If you received or planning to receive funds not through out MYNESTHUB platform and would like to mark this
            payment as "amount received" - this feature is designed specifically for this purpose.
        </p>
    </div>
</div>

<div class="h4 pb-3 border-bottom"><strong id="inputDescription">{{ $invoice->description }}</strong></div>
<div class="row pt-2 pb-3">
    <div class="col-sm-6">
        Due Date: <strong class="float-right" id="inputDueDate">
            {{ \Carbon\Carbon::parse($invoice->due_date)->format("m/d/Y") }}
        </strong>
    </div>
    <div class="col-sm-6">
        Bill Amount: <strong class="float-right" id="inputBillAmount">${{ $invoice->bill_amount }}</strong>
    </div>
</div>
<div class="row justify-content-between pb-3">
    <div class="col-md-6">
        <label for="inputPaidOn">Paid On <i class="required fal fa-asterisk"></i></label>
        <input name="payment_paid_on" id="inputPaidOn" type="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
               class="form-control form-required">
        <span class="invalid-feedback" role="alert">
            <strong>The paid on field is required.</strong>
        </span>
    </div>
    <div class="col-md-6">
        <label for="inputPaidAmount">Paid Amount <i class="required fal fa-asterisk"></i></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">$</div>
            </div>
            <input type="text" class="form-control form-required" name="paid_amount" data-type="currency" maxlength="10"
                   id="inputPaidAmount" value="{{ old('paid_amount') ? old('paid_amount') : $invoice->bill_amount }}">
            <span class="invalid-feedback" role="alert">
                <strong class="amount-error">The paid amount field is required.</strong>
            </span>
        </div>
    </div>
</div>
<div class="mb-2">
    <label for="inputNote">Note</label>
    <textarea type="text" class="form-control" name="payment_note" id="inputNote"></textarea>
</div>
<script>
    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatCurrency(input, blur) {
        var input_val = input.val();
        if (input_val === "") {
            return;
        }
        var original_len = input_val.length;
        // initial caret position
        var caret_pos = input.prop("selectionStart");
        if (input_val.indexOf(".") >= 0) {
            var decimal_pos = input_val.indexOf(".");
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);
            left_side = formatNumber(left_side);
            right_side = formatNumber(right_side);
            if (blur === "blur") {
                right_side += "00";
            }
            right_side = right_side.substring(0, 2);
            input_val = left_side + "." + right_side;
        } else {
            input_val = formatNumber(input_val);
            if (blur === "blur") {
                input_val += ".00";
            }
        }
        input.val(input_val);

        if (blur !== "blur") {
            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }
    }

    jQuery(document).ready(function () {
        jQuery("input[data-type='currency']").each(function () {
            formatCurrency(jQuery(this));
        });

        jQuery("input[data-type='currency']").on({
            keyup: function () {
                formatCurrency(jQuery(this));
            },
            blur: function () {
                formatCurrency(jQuery(this), "blur");
            }
        });
    });
    jQuery('#add_payment input').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
</script>
