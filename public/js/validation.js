
/*input formatting*/
function setupValidatorsForInputs() {
    jQuery("input[data-type='currency']").each(function() {
        formatCurrency(jQuery(this));
    });

    jQuery("input[data-maxamount]").on({
        keyup: function() {
            restrictMaxAmount(jQuery(this));
        }
    });

    jQuery("input[data-type='currency']").on({
        keyup: function() {
            formatCurrency(jQuery(this));
        },
        blur: function() {
            formatCurrency(jQuery(this), "blur");
        }
    });
    jQuery("input[data-type='integer']").on({
        keyup: function() {
            formatInteger(jQuery(this));
        }
    });
}
function formatNumber(n) {
    // format number 1000000 to 1,234,567
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function formatCurrency(input, blur) {
    var input_val = input.val();
    if (input_val === "") { return; }
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

function formatInteger(input) {
    var input_val = input.val();
    if (input_val === "") { return; }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionStart");
    input_val = input_val.replace(/\D/g, "");
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

function restrictMaxAmount(input) {
    var input_value = input.val();
    var numeric_value = Number(input_value.replace(/[^0-9.-]+/g,""));
    var i = 0;
    while(numeric_value > input.data('maxamount')){
        var caret_pos = input.prop("selectionStart");
        var input_value = input_value.slice(0, caret_pos-1) + input_value.slice(caret_pos, input_value.length);
        input.val(input_value);
        caret_pos = caret_pos - 1;
        input[0].setSelectionRange(caret_pos, caret_pos);
        numeric_value = Number(input_value.replace(/[^0-9.-]+/g,""));
        if(i++ > 50){
            break;
        }
    }
}

jQuery(document).ready(function(){
    setupValidatorsForInputs();

    var returnedInvalid = $(".form-control.is-invalid, .customFormAlert").first();
    if(returnedInvalid.length > 0){
        $([document.documentElement, document.body]).animate({
            scrollTop: returnedInvalid.offset().top - 90
        }, 1500);
        if($('#submitError').length > 0) {
            $('#submitError').modal('show');
            setTimeout(function () {
                $('#submitError').modal('hide');
            }, 5000);
        }
    }
});

// Client validation required fields
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                var btn = $(document.activeElement);
                if (!btn.hasClass('woChecks')) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();

                        $("[required]:invalid").each(function () {
                            var feedback_element = $(this).next(".invalid-feedback");
                            if($(this).val() == ''){
                                feedback_element.text(feedback_element.data("fieldname") ? "Field " + feedback_element.data("fieldname") + " is required" : "This field is required");
                            } else {
                                feedback_element.text("Please enter correct value. Tenant should be at least 18 years old.");
                            }
                        });
                        $('[type="email"]:invalid').each(function () {
                            var feedback_element = $(this).next(".invalid-feedback");
                            feedback_element.text("Please enter a valid email address");
                        });
                        var firstInvalid = $("[required]:invalid").first();
                        if (firstInvalid.length > 0) {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: firstInvalid.offset().top - 90
                            }, 1500);

                            if ($('#submitError').length > 0) {
                                $('#submitError').modal('show');
                                setTimeout(function () {
                                    $('#submitError').modal('hide');
                                }, 5000);
                            }
                        }

                    }
                    form.classList.add('was-validated');
                }
            }, false);
        });
    }, false);
})();
