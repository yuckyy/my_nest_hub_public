@if(count($invoices) > 0)
<div class="card dashboardCard mb-4">
<form action="{{ route('mark-as-paid') }}" method="post">
    @csrf

    <div class="card-header justify-content-start">
        Unpaid Rent/Bills

        @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
            <button type="submit" class="btn btn-primary btn-sm btn-pay-invoices ml-4" disabled><i class="fal fa-check mr-2"></i> Mark as Paid</button>
        @endif
    </div>
    <div class="card-body bg-light">

        <div class="table-responsive-md">
            <table class="table snippetTable noWrapHeader">
                <thead>
                    <tr>
                        <th>
                            @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
                                <div class="form-check form-check-inline">
                                    <input name="invoice-all" type="checkbox" value="" class="form-check-input" id="invoice-all" title="Select All">
                                </div>
                            @endif
                        </th>
                        <th>{!! sortableColumn('Tenant Name', 'full_user', 'ajax-negative-invoices') !!}</th>
                        <th>{!! sortableColumn('Email', 'email', 'ajax-negative-invoices') !!}</th>
                        <th>{!! sortableColumn('Property/Unit', 'property_unit', 'ajax-negative-invoices') !!}</th>
                        <th>{!! sortableColumn('Due Date', 'due_date', 'ajax-negative-invoices') !!}</th>
                        <th>{!! sortableColumn('Balance', 'balance', 'ajax-negative-invoices') !!}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="list selectable">
                @foreach($invoices as $invoice)
                    <tr>
                        <td class="actionIconsBlock oneIcon">
                            @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
                                <div class="form-check form-check-inline">
                                    <input name="invoice[]" type="checkbox" value="{{ $invoice->id }}" class="form-check-input invoice-single" id="invoice-{{ $invoice->id }}">
                                </div>
                            @endif
                        </td>
                        <td><a href="{{ route('properties/units/leases', ['unit' => $invoice->unit_id ]) }}" class="text-dark" data-toggle="tooltip" data-placement="top" title="View Lease">{{ $invoice->full_user }}</a></td>
                        <td>
                            <div style="white-space: nowrap">
                                <a href="mailto:{{ $invoice->email }}" class="text-dark" data-toggle="tooltip" data-placement="top" title="Write Email">{{ $invoice->email }}</a>
                                @if (empty($invoice->tenant_last_login))
                                    <span class="ml-1 mr-auto" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Tenant has not registered. Resend an Invitation Email." style="margin: -5px 0">
                                        <button type="button" class="btn btn-light2 btn-sm text-muted showResendModal" data-target="#confirmResendEmailModal" data-full_user="{{ $invoice->full_user }}" data-lease_id="{{ $invoice->lease_id }}">
                                            <i class="fas fa-exclamation-triangle text-danger"></i>
                                        </button>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $invoice->property_unit }}</td>
                        <td>{{ Carbon\Carbon::parse($invoice->due_date)->format('m/d/Y') }}</td>
                        <td class="text-danger">{{ financeCurrencyFormat($invoice->balance) }}</td>
                        <td class="actionIconsBlock">
                            @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
                                <span data-toggle="modal" data-target="#editModal" data-id="{{ $invoice->id }}">
                                    <button type="button" class="btn btn-sm text-success btn-light2" data-toggle="tooltip" data-placement="top" title="Mark as Paid"><i class="fal fa-cog mr-1"></i>Mark as Paid</button>
                                </span>
                            @endif
                            <a href="{{ route('properties/units/payments', ['unit' => $invoice->unit_id]) }}" class="btn btn-sm text-muted btn-light2" data-toggle="tooltip" data-placement="top" title="View Payment"><i class="fal fa-eye"></i></a>
                            <a href="{{ route('properties/units/leases', ['unit' => $invoice->unit_id ]) }}" class="btn btn-sm text-muted btn-light2" data-toggle="tooltip" data-placement="top" title="View Lease"><i class="fal fa-file-signature"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer pb-0 pt-3">
        <div class="float-left mb-3">
            @if (!Auth::user()->isTenant() && !session('adminLoginAsUser'))
                <button type="submit" class="btn btn-primary btn-sm btn-pay-invoices" disabled><i class="fal fa-check mr-2"></i> Mark as Paid</button>
            @endif
        </div>
        <div class="float-right">
            @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator )
                @php
                    $appends = [];
                    if (!empty(Request::get('column'))) $appends['column'] = Request::get('column');
                    if (!empty(Request::get('order'))) $appends['order'] = Request::get('order');
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
</div>
@endif
