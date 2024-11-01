@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="#">Properties</a> > <a href="#">My Big House</a> > <a href="#">Unit 1. Some Unit Name</a>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block"><span class="text-secondary">Unit 1.</span> Some Unit Name</h1>
                    <span class="badge badge-success align-top">Occupied</span>
                    <h6 class="text-center text-sm-left pb-3">5543 Aliquet St. Fort Dodge GA 20783</h6>
                </div>
                <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                    <a href="#" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                    <a href="#" class="btn btn-primary btn-sm"><i class="fal fa-check-circle mr-1"></i> Save</a>
                </div>
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="navTabsLeftContent col-md-9">

                    <form class="needs-validation" novalidate>
                        <div class="card propertyForm propertyFormGeneralInfo">
                            <div class="card-body bg-light">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="validationCustomU01">Name <i class="required fal fa-asterisk"></i></label>
                                        <input type="text" class="form-control" id="validationCustomU02" placeholder="" value="" required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="validationCustomU02">Sq. Footage <i class="required fal fa-asterisk"></i></label>
                                        <input type="text" maxlength="9" data-type="integer" class="form-control" id="validationCustomU02" placeholder="" value="" required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomU04">Bedrooms <i class="required fal fa-asterisk"></i></label>
                                        <select name="property_type" class="custom-select form-control" id="validationCustomU04" required>
                                            <option hidden value=""> </option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                            <option>6</option>
                                            <option>7</option>
                                            <option>8</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomU05">Full Bathrooms <i class="required fal fa-asterisk"></i></label>
                                        <select name="property_type" class="custom-select form-control" id="validationCustomU05" required>
                                            <option hidden value=""> </option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustomU06">Half Bathrooms <i class="required fal fa-asterisk"></i></label>
                                        <select name="property_type" class="custom-select form-control" id="validationCustomU06" required>
                                            <option hidden value=""> </option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="unitDescription1">Description</label>
                                    <textarea class="form-control" id="unitDescription1" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <a href="#" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                                <button href="#" class="btn btn-primary btn-sm float-right"><i class="fal fa-check-circle mr-1"></i> Save</button>
                            </div>
                        </div><!-- /propertyForm -->
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

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

        jQuery(document).ready(function(){
            jQuery("input[data-type='currency']").each(function() {
                formatCurrency(jQuery(this));
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
        });

    </script>
@endsection
