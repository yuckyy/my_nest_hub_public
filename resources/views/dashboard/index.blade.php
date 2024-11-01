@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </div>
    <div class="container-fluid">

        <div class="container-fluid">
            <div class="p-3">
                {{-- In case of put some controls here --}}
            </div>
            {{--@if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session()->get('success') }}
                </div>
            @endif--}}
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="container-fluid">

            @if (Auth::user()->isTenant() || (count(Auth::user()->properties) > 0))
            @else
                <div class="card border-warning propertyForm mb-4">
                    <div class="card-body text-center alert-warning">
                        <p class="m-0">You didn't create any properties yet. Press "Add New Property" to create new property.</p>
                    </div>
                    <div class="card-footer border-warning text-muted text-center">
                        <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                            <i class="fal fa-plus-circle mr-1"></i> Add New Property
                        </a>
                    </div>
                </div>
            @endif


            <div class="row">
                @if ($user->isTenant())<div class="col-xl-5">@else<div class="col-xl-6">@endif

                    <div class="card dashboardCard quickButtonsCard mb-4">
                        <div class="card-header">Quick Buttons</div>
                        <div class="card-body bg-light pb-1">
                            <div class="row">
                                <div class="col-sm mb-3">
                                    <a href="{{ route('profile') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-user"></i>
                                            </div>
                                            <div class="h6">
                                                Account
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm mb-3">
                                    <a href="{{ route('applications') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-file-signature"></i>
                                            </div>
                                            <div class="h6">
                                                Applications
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm mb-3">
                                    <a href="{{ $user->isTenant() ? route('tenant/leases') : route('properties') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                @if ($user->isTenant())
                                                    <i class="fal fa-newspaper"></i>
                                                @else
                                                    <i class="fal fa-home"></i>
                                                @endif
                                            </div>
                                            <div class="h6">
                                                {{ $user->isTenant() ? 'Lease' : 'Properties' }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @if($user->isTenant())
                                @else
                                    <div class="col-sm mb-3">
                                        <a href="{{ route('expenses') }}" class="card">
                                            <div class="card-body p-2 text-center">
                                                <div class="h2 mb-0 mt-2">
                                                    <i class="fal fa-chart-pie"></i>
                                                </div>
                                                <div class="h6">
                                                    Expenses
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm mb-3">
                                    <a href="{{ route('payments') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-dollar-sign"></i>
                                            </div>
                                            <div class="h6">
                                                Payments
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm mb-3">
                                    <a href="{{ route('maintenance') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-tools"></i>
                                            </div>
                                            <div class="h6">
                                                Maintenance
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm mb-3">
                                    <a href="{{ route('profile/finance') }}" class="card">
                                        <div class="card-body p-2 text-center">
                                            <div class="h2 mb-0 mt-2">
                                                <i class="fal fa-credit-card"></i>
                                            </div>
                                            <div class="h6">
                                                Cards
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                @if($user->isTenant())
                                @else
                                    <div class="col-sm-3 mb-3">
                                        <a href="{{ route('fullcalendar') }}" class="card">
                                            <div class="card-body p-2 text-center">
                                                <div class="h2 mb-0 mt-2">
                                                    <i class="fal fa-calendar-alt"></i>
                                                </div>
                                                <div class="h6">
                                                    Calendar
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                @if ($user->isTenant())<div class="col-xl-7">@else<div class="col-xl-6">@endif
                    <div class="row">
                        <div class="col-sm-6">

                            @if ($user->isTenant())
                                <div class="card dashboardCard mb-4">
                                    <div class="card-header">
                                        My Maintenance
                                        <a href="{{ route('maintenance') }}" class="btn btn-light btn-sm text-muted">View All <i class="fal fa-eye ml-1"></i></a>
                                    </div>
                                    <div class="card-body bg-light">
                                        <div class="m-3 pb-1">
                                            <table style="margin: auto">
                                                <tr>
                                                    <td class="p-2">
                                                        <div class="h2 m-0 {{ $countNew == 0 ? 'text-muted' : 'text-danger' }}">
                                                            {{ $countNew }}
                                                        </div>
                                                    </td>
                                                    <td class="text-muted text-left pl-2">
                                                        New Maintenance Requests
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2">
                                                        <div class="h2 m-0 {{ $countInProgress == 0 ? 'text-muted' : 'text-primary2' }}">
                                                            {{ $countInProgress }}
                                                        </div>
                                                    </td>
                                                    <td class="text-muted text-left pl-2">
                                                        In Progress Maintenance Requests
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="p-2">
                                                        <div class="h2 m-0 text-muted">
                                                            {{ $countResolved }}
                                                        </div>
                                                    </td>
                                                    <td class="text-muted text-left pl-2">
                                                        Resolved Maintenance Requests
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    {{--<div class="card-footer text-muted">
                                        TODO Crete new maintenance request etc...
                                        <a href="{{ route('maintenance') }}" class="text-muted d-block">View all</a>
                                    </div>--}}
                                </div>
                            @else
                                <div class="card dashboardCard maintenanceDashboardCard mb-4">
                                    <div class="card-header">
                                        Maintenance Requests
                                        <a href="{{ route('maintenance') }}" class="btn btn-light btn-sm text-muted">View All <i class="fal fa-eye ml-1"></i></a>
                                    </div>
                                    <div class="card-body bg-light p-1 text-center">
                                        <table class="w-75" @if($plan->name == 'Unlimited') style="margin-top: 88px; margin-bottom: 90px" @endif >
                                            <tr>
                                                <td class="w-25">
                                                    <div class="h2 m-0 {{ $countNew == 0 ? 'text-muted' : 'text-danger' }}">
                                                        {{ $countNew }}
                                                    </div>
                                                    <div class="bottomLabelText text-muted">new</div>
                                                </td>
                                                <td class="w-25">
                                                    <div class="h2 m-0 {{ $countInProgress == 0 ? 'text-muted' : 'text-primary2' }}">
                                                        {{ $countInProgress }}
                                                    </div>
                                                    <div class="bottomLabelText text-muted">in progress</div>
                                                </td>
                                                <td class="w-25">
                                                    <div class="h2 m-0 text-muted">
                                                        {{ $countResolved }}
                                                    </div>
                                                    <div class="bottomLabelText text-muted">resolved</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($plan->name != 'Unlimited')
                                    <div class="card upgradeCard mb-4">
                                        <div class="card-header bg-primary text-center text-white p-1">
                                            <div><small>your current plan</small></div>
                                            <div class="h2 mb-1"><span>{{ $plan->name }}</span> {!! planIcon($plan) !!}</div>
                                        </div>
                                        <div class="card-body text-center p-2">
                                            <div class="pb-2 text-danger">Upgrade to get more features</div>
                                            <div class="pb-1">
                                                <a href="{{ route('profile/membership') }}" class="btn btn-danger btn-sm">Upgrade Now</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                        </div>
                        <div class="col-sm-6">

                            <div class="card dashboardCard mb-4">
                                <div class="card-header">
                                    My Payments
                                    <a href="{{ route('payments') }}" class="btn btn-light btn-sm text-muted">View All <i class="fal fa-eye ml-1"></i></a>
                                </div>
                                <div class="card-body bg-light">
                                    @if (Auth::user()->isTenant() && ($leaseCount == 1) && !empty($lease) && !$lease->tenantLinkedFinance())
                                        <div class="m-0 pb-1 text-center">
                                            <div class="h5 m-1">
                                                Deposit
                                            </div>
                                            <a href="{{ route('payments') }}" class="h2 m-0 text-primary2 text-decoration-none">
                                                ${{ $user->deposit }}
                                            </a>
                                        </div>
                                        <div class="m-2 pb-2 text-center">
                                            <div class="h5 m-1">
                                                Outstanding
                                            </div>
                                            <a href="{{ route('payments') }}" class="h2 m-0 text-decoration-none {{ Auth::user()->outstanding == 0 ? 'text-success' : 'text-danger' }}">
                                                ${{ $user->outstanding }}
                                            </a>
                                        </div>
                                        <div class="text-center pt-1">
                                            @if ($lease->landlordLinkedFinance())
                                                <button onclick="window.location.href = '{{ route('recurring-setup',['lease'=>$lease->id]) }}'" class="btn btn-primary btn-sm"><i class="fal fa-stopwatch mr-2"></i> Setup Recurring Payments</button>
                                            @else
                                                <span class="d-inline-block" data-toggle="tooltip" data-placement="top" title="Please contact your landlord">
                                                    <button class="btn btn-primary btn-sm disabled"><i class="fal fa-stopwatch mr-2"></i> Setup Recurring Payments</button>
                                                </span>
                                            @endif
                                        </div>
                                    @else


                                        <!--<div class="inRowComment text-center p-0">
                                            Based on in-progress and completed transactions
                                        </div>-->
                                        <div class="totalBalanceBox pb-0 pt-1">
                                            <div class="totalBalanceLabel">
                                                Deposit
                                            </div>
                                            <div class="totalBalanceAmount totalDeposit last12Month1">
                                                ${{ $user->deposit12 }}
                                            </div>
                                            <div class="totalBalanceAmount totalDeposit last12Month0 d-none">
                                                ${{ $user->deposit }}
                                            </div>
                                        </div>
                                        <div class="totalBalanceBox">
                                            <div class="totalBalanceLabel">
                                                Outstanding
                                            </div>
                                            <div class="totalBalanceAmount {{ $user->outstanding12 > 0 ? 'totalOutstanding' : 'text-success'}} last12Month1">
                                                ${{ $user->outstanding12 }}
                                            </div>
                                            <div class="totalBalanceAmount {{ $user->outstanding > 0 ? 'totalOutstanding' : 'text-success'}} last12Month0 d-none">
                                                ${{ $user->outstanding }}
                                            </div>
                                        </div>

                                        <div class="text-center" style="padding-bottom: 3px">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="last12Month1" value="1" name="last12Month" class="custom-control-input" checked>
                                                <label class="custom-control-label" for="last12Month1">Last 12 Month</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="last12Month0" value="0" name="last12Month" class="custom-control-input">
                                                <label class="custom-control-label" for="last12Month0">View All</label>
                                            </div>
                                        </div>



                                    @endif
                                </div>
                                    {{--<div class="card-footer text-muted">
                                        TODO Crete new payment etc...
                                        <a href="{{ route('payments') }}" class="text-muted d-block">View all</a>
                                    </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->isTenant())
                <div class="card dashboardCard mb-4">
                    <div class="card-header">My Lease</div>
                    <div class="card-body bg-light">
                        @if (isset($lease))
                            <ul class="list-group list-group-flush text-closed">
                                <li class="list-group-item">Start Date <span class="float-right">{!! \Carbon\Carbon::parse($lease->start_date)->format("m/d/Y") !!}</span></li>
                                <li class="list-group-item">End Date <span class="float-right">{!! $lease->end_date ?  \Carbon\Carbon::parse($lease->end_date)->format("m/d/Y") : 'Month To Month' !!}</span></li>
                                <li class="list-group-item">Monthly Due Date <span class="float-right">{!! $lease->monthly_due_date !!}</span></li>
                                <li class="list-group-item">Monthly rent amount <span class="float-right">${!! $lease->amount !!}</span></li>
                            </ul>
                        @else
                            <p class="text-open">You don't have lease yet.</p>
                        @endif
                    </div>
                </div>
            @else
                <div id="invoicesBox"></div>
            @endif
        </div>
    </div>

    @include('includes.units.edit-payments-modal')


    <!-- Resend Email confirmation dialog-->
    <div class="modal fade" id="confirmResendEmailModal" tabindex="-1" role="dialog" aria-labelledby="confirmResendEmailModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmResendEmailModalTitle">Resend an Invitation Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p class="mb-0">Do You want to resend an invitation email to <strong id="modalFullUser"></strong>?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                    <form method="POST" action="{{ route('leases/resend-email') }}">
                        @csrf
                        <input id="leaseId" type="hidden" name="lease" value="">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-paper-plane mr-2"></i> Resend an Invitation Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function setupAjaxLoadedContent(){
            $(".selectable tr input[type=checkbox]").change(function(e){
                if ($(".invoice-single:checked").length == 0) {
                    $('.btn-pay-invoices').prop('disabled',true);
                } else {
                    $('.btn-pay-invoices').prop('disabled',false);
                }
                if (e.target.checked){
                    $(this).closest("tr").addClass("selectedRow");
                } else {
                    $(this).closest("tr").removeClass("selectedRow");
                }
            });
            $(".selectable tr").click(function(e){
                if (e.target.type != 'checkbox' && e.target.tagName != 'BUTTON' && e.target.tagName != 'A'){
                    var cb = $(this).find("input[type=checkbox]");
                    cb.trigger('click');
                }
            });
            $("#invoice-all").change(function(e){
                if (e.target.checked){
                    $(".invoice-single:not(:checked)").each(function(){
                        $(this).trigger('click');
                    });
                } else {
                    $(".invoice-single:checked").each(function(){
                        $(this).trigger('click');
                    });
                }
                if ($(".invoice-single:checked").length == 0) {
                    $('.btn-pay-invoices').prop('disabled',true);
                } else {
                    $('.btn-pay-invoices').prop('disabled',false);
                }
            });
            $("#invoice-all:checked").each(function(){
                $(this).trigger('click');
            });
            $(".invoice-single:checked").each(function(){
                $(this).trigger('click');
            });
            $('.showViewModal').click(function(e){
                e.stopPropagation();
                invoice_id = $(this).data('id');
                var target = $(this).data('target');
                $(target).modal('show')
            });
            $('.showDeleteRecordModal').click(function(e){
                e.stopPropagation();
                var target = $(this).data('target');
                $("#confirmDeleteModal").find('.title').text($(this).data('record-title'));
                $("#confirmDeleteModal").find("input[name='invoice_id']").val($(this).data('record-id'));
                $(target).modal('show');
            });
            $('#invoicesBox [data-toggle="tooltip"]').tooltip();

            $('.showResendModal').click(function(e){
                e.stopPropagation();
                var target = $(this).data('target');
                $('#modalFullUser').html($(this).data('full_user'));
                $('#leaseId').val($(this).data('lease_id'));
                $(target).modal('show')
            });
        }
        jQuery( document ).ready(function($) {
            if(document.getElementById('invoicesBox')){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $("#invoicesBox").load("{{ route('ajax-negative-invoices') . '?r=' . rand(10000000,99999999) }}", function() {
                    setupAjaxLoadedContent();
                });
                $(document).on("click", '#invoicesBox a.page-link, #invoicesBox a.sortLink', function(e) {
                    e.preventDefault();
                    $(".preloader").fadeIn("fast");
                    $("#invoicesBox").load($(this).attr('href'), function() {
                        setupAjaxLoadedContent();
                        $(".preloader").fadeOut("fast");
                    });
                });
                $('#editModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var modal = $(this);
                    $.post("{{ route('edit-payments') }}", {
                        id: button.data('id'),
                    }, function(datajson){
                        $('#add-box').html(datajson.view);
                    });
                });
            }
        });

        {{--
        jQuery( document ).ready(function($) {
            if(document.getElementById('invoicesBox')){
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
                })
            }
        });
        --}}
    </script>
    <script type="text/javascript">
        $(function() {

            $(".progress").each(function() {
                var value = $(this).attr('data-value');
                var left = $(this).find('.progress-left .progress-bar');
                var right = $(this).find('.progress-right .progress-bar');
                if (value > 0) {
                    if (value <= 50) {
                        right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
                    } else {
                        right.css('transform', 'rotate(180deg)')
                        left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
                    }
                }
            });

            function percentageToDegrees(percentage) {
                return percentage / 100 * 360
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('input[name="last12Month"]').change(function(){
                var val = $( 'input[name="last12Month"]:checked' ).val();
                if(val == 1){
                    $('.last12Month1').removeClass('d-none');
                    $('.last12Month0').addClass('d-none');
                } else {
                    $('.last12Month0').removeClass('d-none');
                    $('.last12Month1').addClass('d-none');
                }
            });
            $('#last12Month1').click();
        });
    </script>
@endsection
