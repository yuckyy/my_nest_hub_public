@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Payments</a>
    </div>
    <div class="container-fluid pb-4">
        <div class="container-fluid">
            {{--@if (session('success'))
                <div class="mt-5">
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                </div>
            @endif--}}
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @if ($selectedLease)
                    @include('includes.units.header',['unit'=>$selectedLease->unit])
                @endif
                <div class="leaseFilterToolbar btn-toolbar mb-2 mb-md-0">
                    <div class="input-group input-group-sm mr-0">
                        @if (count($user->leases) > 0)
                            <select class="custom-select custom-select-sm leases-list" onchange="selectLease(this)">
                                <option value="">Current/Previous lease(s)</option>
                                @if (count($activeLeases) > 0)
                                    <optgroup label="Current lease(s)">
                                        @foreach ($activeLeases as $lease)
                                            <option value="{{ $lease->id }}" @if($selectedLease && ($selectedLease->id == $lease->id)) selected="selected" @endif>
                                                Lease:&nbsp;
                                                {{ $lease->custom_start_date }}&nbsp;
                                                -&nbsp;
                                                {{ $lease->custom_end_date }}&nbsp;
                                                Landlord: {{ $lease->unit->property->user->name }} {{ $lease->unit->property->user->lastname }}
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
                                                Landlord: {{ $lease->unit->property->user->name }} {{ $lease->unit->property->user->lastname }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid unitFormContainer">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if(!empty($selectedLease->deleted_at))
                <p class="alert alert-danger">
                    This lease is closed
                </p>
            @endif

            @if ($selectedLease)
                @php
                $lease = $selectedLease;
                @endphp

                @include('payments.lease-payments-partial')

            @else

                <div class="container-fluid pb-4">
                    <div class="container-fluid">
                        <div class="container-fluid unitFormContainer">

                            <div class="propCardWrap col-12">
                                <p class="alert alert-warning">
                                    You don't have any lease yet.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

            @endif
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
        function selectLease(el) {
            var s = $(el).find("option:selected").val();
            window.location.href = "{{ route('payments') }}"+'?lease='+s;
        }
    </script>

    @include('payments.invoices-list-js-partial')

@endsection
