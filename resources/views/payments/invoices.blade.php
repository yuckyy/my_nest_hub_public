@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">
            @if($report_type == 1) Invoices @endif
            @if($report_type == 2) Paid Invoices @endif
            @if($report_type == 3) Unpaid Invoices @endif
            @if($report_type == 4) Upcoming Invoices @endif
        </a>
    </div>
    <div class="container-fluid">
        @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
            <form action="{{ route('mark-as-paid') }}" method="post" id="markAsPaid" class="d-none">
                @csrf
            </form>
        @endif

        @if (Auth::user()->isTenant() || (count(Auth::user()->properties) > 0))

            <form method="get" id="paymentsFilterForm" action="{{ route('payments/invoices') }}">
                <input type="hidden" id="advanced_filter" name="advanced_filter" value="{{ $advanced_filter }}">
                @if($report_type == 2) <input type="hidden" name="paid" value="paid"> @endif
                @if($report_type == 3) <input type="hidden" name="unpaid" value="unpaid"> @endif
                @if($report_type == 4) <input type="hidden" name="upcoming" value="upcoming"> @endif
                <div class="container-fluid">
                    <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                        <div class="text-center text-sm-left">
                            <h1 class="h2 d-inline-block">Payments</h1>
                        </div>

                        <div class="form-inline mr-auto ml-lg-5 mb-3 mb-lg-0">
                            <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Report Type:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <a href="{{ route('payments') }}" class="btn btn-outline-secondary"><i class="fal fa-circle mr-1"></i> Transactions</a>
                                </div>
                                <div class="input-group-append">
                                    <a href="{{ route('payments/invoices', $additional_params) }}" class="btn btn-primary" style="margin-right:2px"><i class="far fa-dot-circle mr-1"></i> {{ $report_text }}</a>
                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item @if($report_type == 1) bg-light @endif" href="{{ route('payments/invoices') }}">Invoices</a>
                                        <a class="dropdown-item @if($report_type == 2) bg-light @endif" href="{{ route('payments/invoices', ['paid'=>'paid']) }}">Paid Invoices</a>
                                        <a class="dropdown-item @if($report_type == 3) bg-light @endif" href="{{ route('payments/invoices', ['unpaid'=>'unpaid']) }}">Unpaid / Partially Paid Invoices</a>
                                        <a class="dropdown-item @if($report_type == 4) bg-light @endif" href="{{ route('payments/invoices', ['upcoming'=>'upcoming']) }}">Upcoming Invoices</a>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="mark-paid btn ml-2" style="display:none" onclick="$('#markAsPaid').submit()"><i class="fal fa-check-circle mr-1"></i>Mark as Paid</button>
                        </div>

                        <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary mr-sm-3 collapseFilters" data-toggle="tooltip" data-placement="top" title="Show Advanced Filters">
                                <i class="fal fa-filter"></i>
                            </a>
                            {{--
                            <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                                <input
                                        name="pay_date"
                                        value="{{ \Request::has('pay_date') ? \Request::get('pay_date') : '' }}"
                                        type="date"
                                        class="form-control form-control-sm"
                                        placeholder="Payment Date"
                                        aria-label="Payment Date"
                                >
                            </div>
                            --}}
                            <div class="mr-sm-3 mb-3 mb-sm-0">
                                {{--<select class="custom-select custom-select-sm" name="unit_id" style="max-width: 200px;">
                                    <option value="">All Properties/Units</option>
                                    @foreach ($units as $unit)
                                        <option data-tokens="{{ $unit->property->address }}/{{ $unit->name }}"
                                                value="{{ $unit->id }}"
                                                @if($unit->id == Request::get('unit_id')) selected="selected" @endif >{{ $unit->property->address }}, {{ $unit->name }}</option>
                                    @endforeach
                                </select>--}}
                                <div class="selectpickerBox form-border rounded">
                                    <select name="property_id_unit_id" class="selectpicker form-control form-control-sm" data-live-search="true">
                                        <option value="">All Properties/Units</option>
                                        @foreach ($properties_units as $unit)
                                            <option data-tokens="{{ $unit->property_address }}/{{ $unit->unit_name ?? "" }}"
                                                    value="{{ $unit->property_id }}_{{ $unit->unit_id }}"
                                                    @if($unit->property_id . "_" . $unit->unit_id == Request::get('property_id_unit_id')) selected="selected" @endif >{{ $unit->property_address }} {{ $unit->unit_name ? ", " . $unit->unit_name : " (All Units)" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="btn-group mr-0 d-block mr-sm-3 d-sm-flex">
                                <input
                                        name="search"
                                        value="{{ \Request::has('search') ? \Request::get('search') : '' }}"
                                        type="text"
                                        class="form-control form-control-sm"
                                        placeholder="Search"
                                        aria-label="Search"
                                        aria-describedby="button-addon2"
                                >
                            </div>
                            <a href="{{ route('payments/invoices', $additional_params) }}" class="btn btn-sm btn-primary d-none d-lg-inline-block"  data-toggle="tooltip" data-placement="top" title="Reset Filters">
                                <i class="fal fa-times"></i>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table noWrapHeader">
                            <thead>
                            <tr>
                                <th>
                                    @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
                                        <div class="form-check form-check-inline">
                                            <input name="invoice-all" type="checkbox" value="" class="form-check-input" id="invoice-all" title="Select All">
                                        </div>
                                    @endif
                                </th>
                                <th>{!! sortableColumn('Due Date', 'due_date', 'payments/invoices', $additional_params) !!}</th>
                                <th>{!! sortableColumn('Balance', 'balance', 'payments/invoices', $additional_params) !!}</th>
                                <th>{!! sortableColumn('Tenant Name', 'full_user', 'payments/invoices', $additional_params) !!}</th>
                                <th>{!! sortableColumn('Email', 'email', 'payments/invoices', $additional_params) !!}</th>
                                <th>{!! sortableColumn('Property/Unit', 'property_unit', 'payments/invoices', $additional_params) !!}</th>
                                <th>{!! sortableColumn('Description', 'description', 'payments/invoices', $additional_params) !!}</th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr class="filtersRow @if ($advanced_filter) active @endif">
                                <th></th>
                                <th><input name="due_date" title="Due Date" value="{{ Request::get('due_date') ? Carbon\Carbon::parse(Request::get('due_date'))->format('Y-m-d') : "" }}" class="form-control form-control-sm" type="date" style="max-width:130px"></th>
                                <th><input name="balance" title="Amount" value="{{ Request::get('balance') }}"  class="form-control form-control-sm" type="text" style="max-width:90px"></th>
                                <th><input name="full_user" title="Tenant Name" value="{{ Request::get('full_user') }}"  class="form-control form-control-sm" type="text"></th>
                                <th><input name="email" title="Email" value="{{ Request::get('email') }}"  class="form-control form-control-sm" type="text"></th>
                                <th><input name="property_unit" title="Property/Unit" value="{{ Request::get('property_unit') }}"  class="form-control form-control-sm" type="text"></th>
                                <th><input name="description" title="Description" value="{{ Request::get('description') }}"  class="form-control form-control-sm" type="text"></th>
                                <th><button type="submit" class="btn btn-primary btn-sm">Apply Filters</button></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="actionIconsBlock oneIcon">
                                        @if (!Auth::user()->isTenant() && !session('adminLoginAsUser') && $invoice->balance < 0)
                                            <div class="form-check form-check-inline">
                                                <input name="invoice[]" type="checkbox" value="{{ $invoice->invoice_id }}" class="form-check-input invoice-single" id="invoice-{{ $invoice->invoice_id }}">
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($invoice->due_date)->format('m/d/Y') }}</td>
                                    <td class="text-{{ $invoice->balance < 0 ? 'danger' : 'success' }}">{{ financeCurrencyFormat($invoice->balance) }}</td>
                                    <td><a href="{{ route('properties/units/leases', ['unit' => $invoice->unit_id, 'lease_id' => $invoice->lease_id  ]) }}" class="text-dark" data-toggle="tooltip" data-placement="top" title="View Lease">{{ $invoice->full_user }}</a></td>
                                    <td><a href="mailto:{{ $invoice->email }}" class="text-dark" data-toggle="tooltip" data-placement="top" title="Write Email">{{ $invoice->email }}</a></td>
                                    <td>{{ $invoice->property_unit }}</td>
                                    <td>{{ $invoice->description }}</td>
                                    <td class="actionIconsBlock">
                                        <span data-toggle="modal" data-target="#detailsModal" data-invoiceid="{{ $invoice->invoice_id }}">
                                            <button type="button" class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="View Details"><i class="fal fa-eye"></i></button>
                                        </span>
                                        <a href="{{ route('properties/units/payments', ['unit' => $invoice->unit_id, 'lease' => $invoice->lease_id ]) }}#invoices" class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="View Lease Invoices"><i class="fal fa-file-invoice-dollar"></i></a>
                                        <a href="{{ route('properties/units/leases', ['unit' => $invoice->unit_id, 'lease_id' => $invoice->lease_id  ]) }}" class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="View Lease"><i class="fal fa-file-signature"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if ($invoices->count() == 0)
                            <p class="alert alert-info">
                                No matches found.
                            </p>
                        @endif
                    </div>
                </div>
            </form>

            <div class="container-fluid">
                @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator )
                    <nav aria-label="Page navigation">
                        @php
                            $appends = [];
                            if (!empty(Request::get('due_date'))) $appends['due_date'] = Request::get('due_date');
                            if (!empty(Request::get('amount'))) $appends['amount'] = Request::get('amount');
                            if (!empty(Request::get('full_user'))) $appends['full_user'] = Request::get('full_user');
                            if (!empty(Request::get('email'))) $appends['email'] = Request::get('email');
                            if (!empty(Request::get('property_unit'))) $appends['property_unit'] = Request::get('property_unit');
                            if (!empty(Request::get('description'))) $appends['description'] = Request::get('description');
                            if (!empty(Request::get('advanced_filter'))) $appends['advanced_filter'] = Request::get('advanced_filter');
                            if (!empty(Request::get('property_id_unit_id'))) $appends['property_id_unit_id'] = Request::get('property_id_unit_id');
                            if (!empty(Request::get('search'))) $appends['search'] = Request::get('search');
                            if (!empty(Request::get('column'))) $appends['column'] = Request::get('column');
                            if (!empty(Request::get('order'))) $appends['order'] = Request::get('order');
                            if (!empty(Request::get('paid'))) $appends['paid'] = Request::get('paid');
                            if (!empty(Request::get('unpaid'))) $appends['unpaid'] = Request::get('unpaid');
                            if (!empty(Request::get('upcoming'))) $appends['upcoming'] = Request::get('upcoming');
                        @endphp
                        @if (!empty($appends))
                            {{ $invoices->appends($appends)->onEachSide(1)->render() }}
                        @else
                            {{ $invoices->onEachSide(1)->render('vendor.pagination.custom') }}
                        @endif
                    </nav>
                @endif
            </div>

        @else
            <div class="p-3">
                <div class="text-center text-sm-left pt-1 pb-2">
                    <h1 class="h2">Payments</h1>
                </div>

                <div class="card border-warning propertyForm">
                    <div class="card-body text-center alert-warning">
                        <p class="m-0">You didn't create any properties yet. Press "Add New Property" to create new property.</p>
                    </div>
                    <div class="card-footer border-warning text-muted text-center">
                        <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                            <i class="fal fa-plus-circle mr-1"></i> Add New Property
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- PAYMENT DETAILS MODAL-->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">Invoice Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="loading">&nbsp;</div>
                    <div class="modal-body-load">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        $( document ).ready(function() {
            $('#paymentsFilterForm').find('select').change(function(){
                $('#paymentsFilterForm').submit();
            });

            $('#detailsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('.loading').show();
                modal.find('.modal-body-load').html('');
                $.post("{{ route('payments/ajax-details') }}", {
                    invoice_id: button.data('invoiceid'),
                    "_token": '{{ csrf_token() }}'
                }, function(data){
                    modal.find('.modal-body-load').html(data);
                    modal.find('.loading').hide();
                });
            });

            $(".collapseFilters").click(function(e) {
                e.preventDefault();
                $('.collapseFilters').tooltip('hide');
                var target = $(".filtersRow");
                target.toggleClass("active");
                if(target.hasClass("active")){
                    $("#advanced_filter").val("1");
                } else {
                    $("#advanced_filter").val("0");
                }
            });

            $(".invoice-single").change(function(e){
                checkInvoices();
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
                checkInvoices();
            });
            if ($(".invoice-single").length == 0) {
                $("#invoice-all").hide();
            }
        });
        function checkInvoices() {
            if ($(".invoice-single:checked").length == 0) {
                $("#invoice-all").prop('checked',false);
                $('.mark-paid').fadeOut();
                $('#markAsPaid input[name="invoice[]"]').remove();
            } else {
                if ($(".invoice-single:checked").length == $(".invoice-single").length) {
                    $("#invoice-all").prop('checked',true);
                }
                $('.mark-paid').fadeIn();
                $('#markAsPaid input[name="invoice[]"]').remove();
                $(".invoice-single:checked").each(function(){
                    $('#markAsPaid').append('<input name="invoice[]" type="checkbox" value="'+$(this).val()+'" checked>')
                })
            }
        }
    </script>
@endsection
