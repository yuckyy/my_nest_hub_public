@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')

    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @include('properties.units.header-partial')

                <div class="leaseFilterToolbar btn-toolbar mb-2 mb-md-0">
                    <div class="input-group input-group-sm mr-0 mr-sm-3">
                        @if (count($unit->leases) > 0)
                        <select class="custom-select custom-select-sm leases-list" onchange="selectLease(this)">
                            <option value="">Current/Previous lease(s)</option>
                            @if (count($activeLeases) > 0)
                                <optgroup label="Current lease">
                                    @foreach ($activeLeases as $lease)
                                        <option value="{{ $lease->id }}" @if($selectedLease && ($selectedLease->id == $lease->id)) selected="selected" @endif>
                                            Lease:&nbsp;
                                            {{ $lease->custom_start_date }}&nbsp;
                                            -&nbsp;
                                            {{ $lease->custom_end_date }}&nbsp;
                                            Tenant: {{ $lease->firstname }} {{ $lease->lastname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if (count($inactiveLeases) > 0)
                                <optgroup label="Previous lease(s)">
                                    @foreach ($inactiveLeases as $lease)
                                        <option value="{{ $lease->id }}" @if($selectedLease && ($selectedLease->id == $lease->id)) selected="selected" @endif>
                                            Lease:&nbsp;
                                            {{ $lease->custom_start_date }}&nbsp;
                                            -&nbsp;
                                            {{ $lease->custom_end_date }}&nbsp;
                                            Tenant: {{ $lease->firstname }} {{ $lease->lastname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        @endif
                    </div>
                    @if (!$unit->isOccupied())
                    <a href="{{ route('leases/add', ['unit' => $unit->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fal fa-plus-circle mr-1"></i> Add New Lease
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @if(!empty($selectedLease->deleted_at))
            <div class="container-fluid pb-2">
                <p class="alert alert-danger">
                    This lease is closed
                </p>
            </div>
        @endif

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="navTabsLeftContainer col-md-3">
                    @include('includes.units.menu')
                </div>

                <div class="navTabsLeftContent col-md-9">
                    @if ($selectedLease)
                        @php
                            $lease = $selectedLease;
                        @endphp

                        @include('payments.lease-payments-partial')

                    @else

                        <div class="card">
                            <div class="card-body bg-light emptyUnitCard">
                                <p class="alert alert-warning">
                                    You didn't create any lease yet. Press "Add New Lease" to create new lease.
                                </p>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($selectedLease)

    <!-- Modals -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalTitle">Invoice Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light" id="view-box">
                    <div class="h4 pb-1"><strong id="dataDescription">-</strong></div>
                    <div class="row no-gutters justify-content-between border-top pt-3">
                        <div class="col-sm-5">
                            Due Date: <strong class="float-right" id="dataDueDate">-</strong>
                        </div>
                        <div class="col-sm-5">
                            Bill Amount: <strong class="float-right" id="dataBillAmount">-</strong>
                        </div>
                    </div>
                    <div class="row no-gutters justify-content-between">
                        <div class="col-sm-5">
                            Paid On: <strong class="float-right" id="dataPaidOn">-</strong>
                        </div>
                        <div class="col-sm-5">
                            Paid Amount: <strong class="float-right" id="dataPaidAmount">-</strong>
                        </div>
                    </div>
                    <div class="row no-gutters justify-content-between pb-3">
                        <div class="col-sm-5">
                            Record Added: <strong class="float-right" id="dataRecordAdded">-</strong>
                        </div>
                        <div class="col-sm-5">
                            Balance: <strong class="float-right text-danger" id="dataBalance">-</strong>
                        </div>
                    </div>
                    <div class="row no-gutters justify-content-between border-top pt-2">
                        <div class="col-sm-1">
                            Note:
                        </div>
                        <div class="col-sm-11">
                            <span id="dataNote"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('includes.units.edit-payments-modal')

    @endif

@endsection

@section('scripts')
    <script src='{{ url('/') }}/vendor/bs-custom-file-input.js'></script>

    <script>
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

    <script>
        $(document).ready(function() {
            $("#cancelAddBill").click(function(e){
                e.preventDefault();
                $("#addBillContent").collapse('hide');
            });

            $("#billType").change(function(){
                var val = $(this).val();
                if (val === "_new"){
                    $("#billType").hide();
                    $("#billTypeOtherBox").show();
                    $("#billTypeOther").focus();
                }
            });
            if ($("#billType").val() === "_new"){
                $("#billType").hide();
                $("#billTypeOtherBox").show();
                $("#billTypeOther").focus();
            }
            $("#billTypeCancel").click(function(e){
                e.preventDefault();
                $("#billType").show();
                $("#billTypeOtherBox").hide();
                $("#billType").val("");
                $("#billTypeOther").val("");
            });
            // $("#billType").show();
            // $("#billTypeOtherBox").hide();
            // $("#billTypeOther").val("");
            // $("#billType").val("");
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#editModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                $.post("{{ route('edit-payments') }}", {
                    id: button.data('id'),
                }, function(datajson){
                    $('#add-box').html(datajson.view);
                });
            });
        });
        $('#add_payment').submit(function (e) {
            var valid = true;
            $(this).find('.form-required').each(function(){
                if ($(this).val() == '') {
                    $(this).addClass('is-invalid');
                    valid = false;
                }
            });
            var s = $(this).find('input[name=paid_amount]').val().replace(/^0+/, '');
            const regex = /,/gi;
            s = s.replaceAll(regex, '');
            if (parseFloat(s) > parseFloat($(this).find('input[name=bill_amount]').val())) {
                $(this).find('input[name=paid_amount]').addClass('is-invalid');
                $(this).find('.amount-error').html('The paid amount can not be greater than bill amount.');
                valid = false;
            }
            if (!valid) {
                e.preventDefault();
            }
        });
        function selectLease(el) {
            var s = $(el).find("option:selected").val();
            window.location.href = "{{ route('properties/units/payments', ['unit' => $unit->id]) }}"+'?lease='+s;
        }
    </script>

    @include('payments.invoices-list-js-partial')

@endsection
