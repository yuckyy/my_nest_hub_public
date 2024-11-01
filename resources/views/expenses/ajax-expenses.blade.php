<div class="card dashboardCard mb-4">
    <div class="card-header">
        Expenses
        @if($isPropertyPage)
            <div class="ml-auto">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="parent" id="parent_field_1" value="property" {{ Request::get('parent') == 'property' ? 'checked' : '' }}>
                    <label class="form-check-label" for="parent_field_1">Based on Property</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="parent" id="parent_field_2" value="unit" {{ Request::get('parent') == 'unit' ? 'checked' : '' }}>
                    <label class="form-check-label" for="parent_field_2">Based on Units</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="parent" id="parent_field_3" value="" {{ Request::get('parent') ? '' : 'checked' }}>
                    <label class="form-check-label" for="parent_field_3">All</label>
                </div>
            </div>
        @else
            <input name="parent" value="unit" type="hidden">
        @endif
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary ml-5 collapseFilters" data-toggle="tooltip" data-placement="top" title="Show Advanced Filters">
            <i class="fal fa-filter"></i>
        </a>
    </div>

    <div class="card-body bg-light">

        <div class="table-responsive-md">
            <table class="table snippetTable noWrapHeader">
                <thead>
                    <tr>
                        <th>{!! sortableColumn('Date', 'expense_date', 'ajax-expenses', ['property_id' => Request::get('property_id'), 'unit_id' => Request::get('unit_id'), 'parent' => Request::get('parent')]) !!}</th>
                        <th>{!! sortableColumn('Name', 'name', 'ajax-expenses', ['property_id' => Request::get('property_id'), 'unit_id' => Request::get('unit_id'), 'parent' => Request::get('parent')]) !!}</th>
                        <th>{!! sortableColumn('Amount', 'amount', 'ajax-expenses', ['property_id' => Request::get('property_id'), 'unit_id' => Request::get('unit_id'), 'parent' => Request::get('parent')]) !!}</th>
                        <th>{!! sortableColumn('Repeated Monthly', 'monthly', 'ajax-expenses', ['property_id' => Request::get('property_id'), 'unit_id' => Request::get('unit_id'), 'parent' => Request::get('parent')]) !!}</th>
                        @if($isPropertyPage)
                            <th>{!! sortableColumn('Unit', 'unit_name', 'ajax-expenses', ['property_id' => Request::get('property_id'), 'unit_id' => Request::get('unit_id'), 'parent' => Request::get('parent')]) !!}</th>
                        @endif
                        <th>&nbsp;</th>
                    </tr>
                    <tr class="filtersRow @if (Request::get('advanced_filter')) active @endif">
                        <th><input id="expense_date_field" name="expense_date" title="Date" value="{{ Request::get('expense_date') ? Carbon\Carbon::parse(Request::get('expense_date'))->format('Y-m-d') : "" }}" class="form-control form-control-sm" type="date" style="max-width:130px"></th>
                        <th><input id="name_field" name="name" title="Name" value="{{ Request::get('name') }}"  class="form-control form-control-sm" type="text"></th>
                        <th><input id="amount_field" name="amount" title="Amount" value="{{ Request::get('amount') }}"  class="form-control form-control-sm" type="text" style="max-width:90px"></th>
                        <th>
                            <select id="monthly_field" name="monthly" title="Repeated Monthly" class="form-control form-control-sm" type="text" style="max-width:90px">
                                <option></option>
                                <option {{ Request::get('monthly') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option {{ Request::get('monthly') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </th>
                        @if($isPropertyPage)
                            <th>&nbsp;</th>
                        @endif
                        <th><button type="button" id="applyFilters" class="btn btn-primary btn-sm">Apply Filters</button></th>
                    </tr>
                </thead>
                <tbody class="list selectable">
                @foreach($expenses as $expense)
                    <tr>
                        <td>
                            {{ Carbon\Carbon::parse($expense->expense_date)->format('m/d/Y') }}
                        </td>
                        <td>{{ $expense->name }}</td>
                        <td>${{ $expense->amount }}</td>
                        <td>{{ $expense->monthly ? 'Yes' : 'No' }}</td>
                        @if($isPropertyPage)
                            <td>
                                @if($expense->unit_name == '-')
                                    -
                                @else
                                    <a class="text-dark" href="{{ route('properties/units/expenses',['unit_id' => $expense->unit_id]) }}"><u>{{ $expense->unit_name }}</u></a>
                                @endif
                            </td>
                        @endif
                        <td class="actionIconsBlock">
                            <span class="showDeleteRecordModal" data-target="#confirmDeleteModal" data-record-id="{{ $expense->id }}" data-record-title="expenses: {{ $expense->name }}, {{ Carbon\Carbon::parse($expense->expense_date)->format('m/d/Y') }}, ${{ $expense->amount }}" >
                                <button type="button" data-toggle="tooltip" data-placement="top" title="Delete Expense" class="btn btn-sm btn-light2 mr-1 text-danger"><i class="fal fa-trash-alt"></i></button>
                            </span>
                            <span class="showViewModal" data-target="#detailsModal" data-id="{{ $expense->id }}">
                                <button type="button" class="btn btn-sm text-muted" data-toggle="tooltip" data-placement="top" title="View Details"><i class="fal fa-eye"></i></button>
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($expenses instanceof \Illuminate\Pagination\LengthAwarePaginator )
        @if($expenses->hasPages())
            <div class="card-footer pb-0 pt-3 ">
                @php
                    $appends = [];
                    if (!empty(Request::get('column'))) $appends['column'] = Request::get('column');
                    if (!empty(Request::get('order'))) $appends['order'] = Request::get('order');
                    if (!empty(Request::get('unit_id'))) $appends['unit_id'] = Request::get('unit_id');
                    if (!empty(Request::get('property_id'))) $appends['property_id'] = Request::get('property_id');
                    if (!empty(Request::get('advanced_filter'))) $appends['advanced_filter'] = Request::get('advanced_filter');
                    if (!empty(Request::get('expense_date'))) $appends['expense_date'] = Request::get('expense_date');
                    if (!empty(Request::get('name'))) $appends['name'] = Request::get('name');
                    if (!empty(Request::get('amount'))) $appends['amount'] = Request::get('amount');
                    if (!empty(Request::get('parent'))) $appends['parent'] = Request::get('parent');
                    if (!empty(Request::get('monthly'))) $appends['monthly'] = Request::get('monthly');
                    if (!empty(Request::get('unit_name'))) $appends['unit_name'] = Request::get('unit_name');
                @endphp
                @if (!empty($appends))
                    {{ $expenses->appends($appends)->onEachSide(1)->render() }}
                @else
                    {{ $expenses->onEachSide(1)->render('vendor.pagination.custom') }}
                @endif
            </div>
        @endif
    @endif
    <input type="hidden" id="advanced_filter" name="advanced_filter" value="{{ Request::get('advanced_filter') }}">
</div>
