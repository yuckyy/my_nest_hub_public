@if( count($invoices) > 0 || !empty(Request::get('advanced_filter')) || !empty(Request::get('paid')))
<div class="card dashboardCard mb-4">
    <div class="card-header mobileNotJustify">
        Invoices / Additional Payments

        <div class="ml-md-auto">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="paid" id="paid_field_1" value="unpaid" {{ Request::get('paid') == 'unpaid' ? 'checked' : '' }}>
                <label class="form-check-label" for="paid_field_1">Unpaid</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="paid" id="paid_field_2" value="paid" {{ Request::get('paid') == 'paid' ? 'checked' : '' }}>
                <label class="form-check-label" for="paid_field_2">Paid</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="paid" id="paid_field_3" value="" {{ Request::get('paid') ? '' : 'checked' }}>
                <label class="form-check-label" for="paid_field_3">All</label>
            </div>
        </div>

        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary ml-5 collapseFilters" data-toggle="tooltip" data-placement="top" title="Show Advanced Filters">
            <i class="fal fa-filter"></i>
        </a>
    </div>
    <form action="{{ route('mark-as-paid') }}" method="post">
        @csrf

        <div class="card-body bg-light invoicesCard">

            <div class="table-responsive maxResponsiveFix">
                <table class="table snippetTable noWrapHeader">
                    <thead>
                        <tr>
                            <th>
                                @if (!session('adminLoginAsUser'))
                                    <div class="form-check form-check-inline">
                                        <input name="invoice-all" type="checkbox" value="" class="form-check-input" id="invoice-all" title="Select All">
                                    </div>
                                @endif
                            </th>
                            <th>{!! sortableColumn('Due Date', 'due_date', 'ajax-invoices', ['lease_id' => Request::get('lease_id'), 'paid' => Request::get('paid')]) !!}</th>
                            <th>{!! sortableColumn('Description', 'description', 'ajax-invoices', ['lease_id' => Request::get('lease_id'), 'paid' => Request::get('paid')]) !!}</th>
                            <th>{!! sortableColumn('Expected / Received', 'amount', 'ajax-invoices', ['lease_id' => Request::get('lease_id'), 'paid' => Request::get('paid')]) !!}</th>
                            <th>{!! sortableColumn('Balance', 'balance', 'ajax-invoices', ['lease_id' => Request::get('lease_id'), 'paid' => Request::get('paid')]) !!}</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr class="filtersRow @if (Request::get('advanced_filter')) active @endif">
                            <th>&nbsp;</th>
                            <th><input id="due_date_field" name="due_date" title="Due Date" value="{{ Request::get('due_date') ? Carbon\Carbon::parse(Request::get('due_date'))->format('Y-m-d') : "" }}" class="form-control form-control-sm" type="date" style="max-width:130px"></th>
                            <th><input id="description_field" name="description" title="Description" value="{{ Request::get('description') }}"  class="form-control form-control-sm" type="text"></th>
                            <th><input id="amount_field" name="amount" title="Amount" value="{{ Request::get('amount') }}"  class="form-control form-control-sm" type="text" style="max-width:90px"></th>
                            <th><input id="balance_field" name="balance" title="Balance" value="{{ Request::get('balance') }}"  class="form-control form-control-sm" type="text" style="max-width:90px"></th>
                            <th><button type="button" id="applyFilters" class="btn btn-primary btn-sm">Apply Filters</button></th>
                        </tr>
                    </thead>
                    <tbody class="list selectable">
                    @foreach($invoices as $invoice)
                        <tr>
                            <td class="actionIconsBlock oneIcon">
                                @if (!session('adminLoginAsUser'))
                                    <div class="form-check form-check-inline">
                                        <input name="invoice[]" type="checkbox" value="{{ $invoice->id }}" class="form-check-input invoice-single" id="invoice-{{ $invoice->id }}">
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{ Carbon\Carbon::parse($invoice->due_date)->format('m/d/Y') }}
                                @if(!empty($invoice->pay_month))
                                    (For {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->pay_month)->format('F Y') }})
                                @endif
                            </td>
                            <td>{{ $invoice->description }}</td>
                            <td>$<span class="expected">{{ $invoice->amount }}</span> / $<span class="received">{{ $invoice->paid_amount }}</span></td>
                            <td class="{{ $invoice->balance >= 0 ? 'text-success' : 'text-danger' }}">{{ financeCurrencyFormat($invoice->balance) }}</td>
                            <td class="actionIconsBlock wide200">
                                @if (!session('adminLoginAsUser'))
                                    @if ($invoice->balance < 0)
                                        <span data-toggle="modal" data-target="#editModal" data-id="{{ $invoice->id }}">
                                            <button type="button" class="btn btn-sm btn-light2 text-success"><i class="fal fa-cog mr-1"></i>Mark as Paid</button>
                                        </span>
                                    @endif
                                    @if ($invoice->is_late_fee == 1 || ($invoice->is_lease_pay == 0 && $invoice->balance == -$invoice->amount))
                                        <span class="showDeleteRecordModal" data-target="#confirmDeleteModal" data-record-id="{{ $invoice->id }}" data-record-title="invoice: {{ $invoice->description }}, {{ Carbon\Carbon::parse($invoice->due_date)->format('m/d/Y') }}, ${{ $invoice->amount }}" >
                                            <button type="button" data-toggle="tooltip" data-placement="top" title="Delete Invoice" class="btn btn-sm btn-light2 mr-1 text-danger"><i class="fal fa-trash-alt"></i></button>
                                        </span>
                                    @endif
                                @endif
                                <span class="showViewModal" data-target="#detailsModal" data-id="{{ $invoice->id }}">
                                    <button type="button" class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="View Details"><i class="fal fa-eye"></i></button>
                                </span>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer pb-0 pt-3 ">
            <div class="float-left mb-3">
                @if (!session('adminLoginAsUser'))
                    <button type="submit" class="btn btn-primary btn-sm btn-pay-invoices" disabled><i class="fal fa-check mr-2"></i> Mark as Paid</button>
                @endif
            </div>
            <div class="float-right">
                @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator )
                    @php
                        $appends = [];
                        if (!empty(Request::get('column'))) $appends['column'] = Request::get('column');
                        if (!empty(Request::get('order'))) $appends['order'] = Request::get('order');
                        if (!empty(Request::get('lease_id'))) $appends['lease_id'] = Request::get('lease_id');
                        if (!empty(Request::get('advanced_filter'))) $appends['advanced_filter'] = Request::get('advanced_filter');
                        if (!empty(Request::get('due_date'))) $appends['due_date'] = Request::get('due_date');
                        if (!empty(Request::get('description'))) $appends['description'] = Request::get('description');
                        if (!empty(Request::get('amount'))) $appends['amount'] = Request::get('amount');
                        if (!empty(Request::get('balance'))) $appends['balance'] = Request::get('balance');
                        if (!empty(Request::get('paid'))) $appends['paid'] = Request::get('paid');
                    @endphp
                    @if (!empty($appends))
                        {{ $invoices->appends($appends)->onEachSide(1)->render() }}
                    @else
                        {{ $invoices->onEachSide(1)->render('vendor.pagination.custom') }}
                    @endif
                @endif
            </div>
        </div>
    </form>
    <input type="hidden" id="advanced_filter" name="advanced_filter" value="{{ Request::get('advanced_filter') }}">
</div>
@endif
