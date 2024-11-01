@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ url('properties') }}">
            Properties
        </a>
        >
        <a href="{{route('properties/edit', ['id' => $property->id])}}">
            {{$property->address}}
        </a>
    </div>

    <div class="container-fluid pb-4">
        @include('properties.edit-address-partial')

        <div class="container-fluid">
            <div class="row">

                <div class="col-md-9 order-md-last mb-4 mb-md-0">

                    <ul class="nav nav-tabs propertyTabs">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('properties/edit', ['property' => $property->id]) }}">Unit
                                Information</a>
                        </li>
                        <li class="nav-item mobileActive">
                            <span class="nav-link active"
                                  data-href="{{ route('properties/expenses', ['property' => $property->id]) }}">Expenses & Profit</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('properties/documents', ['property' => $property->id]) }}">Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('properties/operations', ['property' => $property->id]) }}">Advanced</a>
                        </li>
                    </ul>
                    <div class="card propertyForm mb-4">
                        <div class="accHeader collapsed card-header border-bottom-0 d-flex justify-content-between"
                             id="headingSummary" data-toggle="collapse" data-target="#collapseSummary">
                            <div>
                                <i class="fal fa-coins mr-1 h5 mb-0 d-inline-block align-middle"></i> <span
                                    class="d-inline-block align-middle">Summary</span>
                            </div>
                            <button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse"
                                    data-target="#collapseSummary" aria-expanded="true" aria-controls="collapseSummary"
                                    style="margin: -5px 0">
                                <span>Show  <i class="fal fa-eye ml-1"></i></span>
                                <span class="d-none">Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                            </button>
                        </div>
                        <div id="collapseSummary" class="collapse marketingCollapseItem"
                             aria-labelledby="headingSummary">
                            <div class="card-body border-top bg-light">
                                <div class="row text-center">
                                    <div class="col-md">
                                        <div class="h6 mb-1 text-success">
                                            Income
                                        </div>
                                        <div class="h2 mb-0 text-success last12Month1">
                                            ${{ $property->totalIncome12() }}
                                        </div>
                                        <div class="h2 mb-0 text-success last12Month0 d-none">
                                            ${{ $property->totalIncome() }}
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="h6 mb-1 text-danger">
                                            Expenses
                                        </div>
                                        <div class="h2 text-danger last12Month1">
                                            ${{ $property->totalExpenses12() }}
                                        </div>
                                        <div class="h2 text-danger last12Month0 d-none">
                                            ${{ $property->totalExpenses() }}
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="h6 mb-1 text-primary2">
                                            Net Profit/Loss
                                        </div>
                                        <div
                                            class="h2 {{ $property->totalIncome12() > $property->totalExpenses12() ? 'text-primary2' : 'text-danger'}} last12Month1">
                                            {{ financeCurrencyFormat($property->totalIncome12() - $property->totalExpenses12()) }}
                                        </div>
                                        <div
                                            class="h2 {{ $property->totalIncome() > $property->totalExpenses() ? 'text-primary2' : 'text-danger'}} last12Month0 d-none">
                                            {{ financeCurrencyFormat($property->totalIncome() - $property->totalExpenses()) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="last12Month1" value="1" name="last12Month"
                                               class="custom-control-input" checked>
                                        <label class="custom-control-label" for="last12Month1">Last 12 Month</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="last12Month0" value="0" name="last12Month"
                                               class="custom-control-input">
                                        <label class="custom-control-label" for="last12Month0">View All</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- $sql --}}
                        <div
                            class="accHeader collapsed card-header border-top border-bottom-0 d-flex justify-content-between"
                            id="headingChart" data-toggle="collapse" data-target="#collapseChart">
                            <div>
                                <i class="fal fa-chart-bar mr-1 h5 mb-0 d-inline-block align-middle"></i> <span
                                    class="d-inline-block align-middle">Chart</span>
                            </div>
                            <button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse"
                                    data-target="#collapseChart" aria-expanded="true" aria-controls="collapseChart"
                                    style="margin: -5px 0">
                                <span>Show  <i class="fal fa-eye ml-1"></i></span>
                                <span class="d-none">Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                            </button>
                        </div>
                        <div id="collapseChart" class="collapse marketingCollapseItem" aria-labelledby="headingChart">
                            <div class="card-body border-top bg-light">
                                <canvas id="chBar"></canvas>
                            </div>
                        </div>

                    </div><!-- /propertyForm -->
                    <div class="card propertyForm mb-4">

                        <div class="card-header">
                            Add Expenses to The Entire Property
                            <div class="inRowComment pt-1 pb-0">
                                <i class="fal fa-info-circle"></i> Adding expense(s) into this section will be applied
                                for the entire property. If you would like to add expense for a given unit, not for the
                                entire property, then navigate to the Unit Information tab and click on View/Edit
                                Expense.
                            </div>
                        </div>
                        <form class="checkUnload" method="POST" action="{{ route('add-expense') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="p-3 bg-light">

                                <!--<div class="inRowComment">
                                    <i class="fal fa-info-circle"></i>
                                    If tenant is registered in MYNESTHUB, our platform will send an email notification to the tenant about added bill. Tenant will also have an ability to view given bill on our platform.
                                </div>-->
                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label for="billType">Expense Type <i
                                                class="required fal fa-asterisk"></i></label>
                                        <div id="org_div3">
                                            <div id="org_div2">
                                            </div>
                                        </div>
                                        <select data-toggle="dropdown" id="org_div1" name="expense_type"
                                                data-toggle="collapse" id="billType" data-target="#main_nav"
                                                class=" dropdown custom-select form-control @error('expense_type') is-invalid @enderror @error('expense_name') is-invalid @enderror">
                                        </select>
                                        <input id="org_div4" name="pid" class="d-none form-control ">
                                        <div class="collapse navbar-collapse divdropstyle list" id="main_nav">
                                            <ul class="navbar-nav " style="background-color: #fff">
                                                <li class="nav-item ">
                                                    <ul class="dropdown-menu dropdown-menu-drop uldropstyles">
                                                        @foreach( $allcategory as $category)
                                                            <li><a class="dropdown-item" disabled> {{$category->name}}
                                                                    ></a>
                                                                <ul class="submenu dropdown-menu ulsubdropstyle">
                                                                    @foreach( $allsubcategory as $subcategory)
                                                                        @if($subcategory->pid === $category->id)
                                                                            <li>
                                                                                <a class="dropdown-item dropdown-item-click"
                                                                                   data-pid="{{$subcategory->pid}}"
                                                                                   data-category="{{$subcategory->id}}"> {{$subcategory->name}}</a>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                        {{--                                        <label for="billType">Expense Type <i class="required fal fa-asterisk"></i></label>--}}
                                        {{--                                        <select name="expense_type" class="custom-select form-control @error('expense_type') is-invalid @enderror @error('expense_name') is-invalid @enderror" id="billType">--}}
                                        {{--                                            <option value="">Select Expense Type</option>--}}
                                        {{--                                            @foreach ($user->expensesTypes() as $item)--}}
                                        {{--                                                <option value="{{ $item->id }}" {{ old('expense_type') && old('expense_type') ==  $item->id ? 'selected' : '' }}>{{ $item->name }}</option>--}}
                                        {{--                                            @endforeach--}}
                                        {{--                                            <option value="_new" {{ old('expense_type') && old('expense_type') ==  '_new' ? 'selected' : '' }}>Other</option>--}}
                                        {{--                                        </select>--}}
                                        {{--                                        <div style="display: none" class="input-group" id="billTypeOtherBox">--}}
                                        {{--                                            <input id="billTypeOther" type="text" name="expense_name" class="form-control" placeholder="Enter Expense Type" aria-label="Enter Expense Type" value="{{ old('expense_name') }}">--}}
                                        {{--                                            <div class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="billTypeCancel"><i class="fal fa-times"></i></button>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        @error('expense_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        @error('expense_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="expense_amount">Amount <i
                                                class="required fal fa-asterisk"></i></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$</div>
                                            </div>
                                            <input type="text"
                                                   class="form-control @error('expense_amount') is-invalid @enderror"
                                                   name="expense_amount" id="expense_amount" data-type="currency"
                                                   maxlength="10" value="{{ old('expense_amount') ?? '' }}">
                                            @error('expense_amount')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="expense_date">Date <i class="required fal fa-asterisk"></i></label>
                                        <input name="expense_date" id="expense_date" type="date"
                                               value="{{ old('expense_date') ? old('expense_date') : \Carbon\Carbon::now()->addDays(5)->format('Y-m-d') }}"
                                               class="form-control @error('expense_date') is-invalid @enderror">
                                        @error('expense_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label>&nbsp;</label>
                                        <div class="custom-control custom-checkbox pt-2 ml-2">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input @error('monthly') is-invalid @enderror"
                                                id="monthly"
                                                name="monthly"
                                            >
                                            <label id="monthlyLabel" class="custom-control-label" for="monthly">
                                                Repeat Monthly
                                            </label>
                                            <span class="invalid-feedback" role="alert">
                                                @error('monthly')
                                                {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div id="collapseMonthly" aria-expanded="false" class="collapse">
                                    <div class="form-row justify-content-end">
                                        <div class="col-md-3 mb-3">
                                            <label for="endDate">End Date</label>
                                            <input
                                                type="date"
                                                value="{{ old('end_date') }}"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                id="endDate"
                                                name="end_date"
                                                disabled
                                            >
                                            <span class="invalid-feedback" role="alert">
                                                @error('end_date')
                                                {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>&nbsp;</label>
                                            <div class="custom-control custom-checkbox pt-2 ml-2">
                                                <input
                                                    type="checkbox"
                                                    class="custom-control-input @error('no_end_date') is-invalid @enderror"
                                                    id="no_end_date"
                                                    name="no_end_date"
                                                    checked
                                                >
                                                <label class="custom-control-label" for="no_end_date">
                                                    No end date
                                                </label>
                                                <span class="invalid-feedback" role="alert">
                                                    @error('no_end_date')
                                                    {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-9 mb-3">
                                        <label for="notesField">Notes</label>
                                        <textarea style="height: 34px" title="Notes" class="form-control"
                                                  id="notesField" name="notes"
                                                  maxlength="4000">{{ old('notes') }}</textarea>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="expense_file">Document</label>
                                        <div class="custom-file customSingleFile">
                                            <input type="file" class="custom-file-input" id="expense_file"
                                                   name="expense_file">
                                            <label for="expense_file" class="custom-file-label" data-browse="">Upload
                                                File</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 bg-white text-muted border-top text-right">
                                <button class="btn btn-primary btn-sm"><i class="fal fa-check-circle mr-1"></i> Add
                                    Expenses
                                </button>
                            </div>
                        </form>
                    </div>

                    <a name="invoices"></a>

                    @if($expensesCount > 0)
                        <div id="expensesBox"></div>
                    @endif

                    <div class="card propertyForm mb-4">
                        <div class="card-header cardHeaderMulti d-sm-flex justify-content-between">
                            <div>Expenses Per Unit</div>
                            <div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="last12Month1-2" value="1" name="last12Month-2"
                                           class="custom-control-input" checked>
                                    <label class="custom-control-label" for="last12Month1-2">Summary For Last 12
                                        Month</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline mr-0">
                                    <input type="radio" id="last12Month0-2" value="0" name="last12Month-2"
                                           class="custom-control-input">
                                    <label class="custom-control-label" for="last12Month0-2">View All</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="newFullData">

                            @foreach ($units as $key => $unit)
                                <div class="card unitCard mb-3" id="newUnit{{$unit->id}}">
                                    <div class="d-block d-sm-table propCardTable">
                                        <div class="d-block d-sm-table-row">
                                            <a href="{{ route('properties/units/expenses', ['unit_id' => $unit->id]) }}"
                                               class="cardImgSell d-block d-sm-table-cell text-center text-secondary p-0"
                                               @if($unit->imageUrl())
                                                   style="background-image: url({{ $unit->imageUrl() }});"
                                                @endif
                                            >
                                                <div class="h2">
                                                    <i class="fal fa-door-open"></i>
                                                </div>
                                            </a>

                                            <div class="cardBodySell d-block d-sm-table-cell">
                                                <div class="cardBody p-2">
                                                    <div class="ml-2">
                                                        <span class="h5">{{ $unit->name }}</span>
                                                        @if ($unit->isOccupied())
                                                            <span class="badge badge-danger">Occupied</span>
                                                        @else
                                                            <span class="badge badge-success">Vacant</span>
                                                        @endif
                                                    </div>

                                                    <div class="propCardSmallText">
                                                        <a href="{{ route('properties/units/expenses', ['unit_id' => $unit->id]) }}"
                                                           title="Expenses & Profit"
                                                           class="btn btn-sm btn-light mr-1 text-muted"><i
                                                                class="fas fa-chart-pie"></i><span> Expenses & Profit</span></a>
                                                        <a href="{{ route('payments', ['property_id_unit_id' => $property->id . "_" . $unit->id]) }}"
                                                           title="Payments"
                                                           class="btn btn-sm btn-light mr-1 text-muted"><i
                                                                class="fas fa-dollar-sign"></i><span> Payments</span></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-block d-sm-table-cell">
                                                <div
                                                    class="last12Month0-2 d-none text-sm-right expensesBox ml-auto mr-4 mb-2">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div><small>Income</small></div>
                                                            <div
                                                                class="h5 {{ $unit->totalIncome() == 0 ? 'text-muted' : 'text-success'}}">
                                                                ${{ $unit->totalIncome() }}</div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div><small>Expenses</small></div>
                                                            <div
                                                                class="h5 {{ $unit->totalExpenses() == 0 ? 'text-muted' : 'text-danger'}}">
                                                                ${{ $unit->totalExpenses() }}</div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div><small>Net Profit/Loss</small></div>
                                                            <div
                                                                class="h5 {{ $unit->totalIncome() == $unit->totalExpenses() ? 'text-muted' : ( $unit->totalIncome() > $unit->totalExpenses() ? 'text-primary2' : 'text-danger' ) }}">
                                                                {{ financeCurrencyFormat($unit->totalIncome() - $unit->totalExpenses()) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="last12Month1-2 text-sm-right expensesBox ml-auto mr-4 mb-2">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div><small>Income</small></div>
                                                            <div
                                                                class="h5 {{ $unit->totalIncome12() == 0 ? 'text-muted' : 'text-success'}}">
                                                                ${{ $unit->totalIncome12() }}</div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div><small>Expenses</small></div>
                                                            <div
                                                                class="h5 {{ $unit->totalExpenses12() == 0 ? 'text-muted' : 'text-danger'}}">
                                                                ${{ $unit->totalExpenses12() }}</div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div><small>Net Profit/Loss</small></div>
                                                            <div
                                                                class="h5 {{ $unit->totalIncome12() == $unit->totalExpenses12() ? 'text-muted' : ( $unit->totalIncome12() > $unit->totalExpenses12() ? 'text-primary2' : 'text-danger' ) }}">
                                                                {{ financeCurrencyFormat($unit->totalIncome12() - $unit->totalExpenses12()) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{--
                                            <div class="d-block d-sm-table-cell text-right">
                                                <div class="h5">
                                                    <span class="mr-3">
                                                        Income: <span class="{{ $unit->totalIncome() == 0 ? 'text-muted' : 'text-success'}}">${{ $unit->totalIncome() }}</span>
                                                    </span>
                                                    <span class="mr-3">
                                                        Expenses: <span class="{{ $unit->totalExpenses() == 0 ? 'text-muted' : 'text-danger'}}">${{ $unit->totalExpenses() }}</span>
                                                    </span>
                                                    <span class="mr-3">
                                                        Net Profit/Loss: <span class="{{ $unit->totalIncome() == $unit->totalExpenses() ? 'text-muted' : ( $unit->totalIncome() > $unit->totalExpenses() ? 'text-primary2' : 'text-danger' ) }}">
                                                            {{ financeCurrencyFormat($unit->totalIncome() - $unit->totalExpenses()) }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div><!-- /col-md-9 -->

                <div class="col-md-3 order-md-first">
                    @include('properties.edit-photos-partial')
                </div>

            </div>
        </div>
    </div>

    <!-- DELETE RECORD confirmation dialog-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>You are about to delete <b><i class="title"></i></b>, this procedure is irreversible.</p>
                    <div>Do you want to proceed?</div>
                </div>
                <div class="modal-footer">
                    <form class="remove-invoice" method="POST" action="{{ route('remove-expense') }}">
                        @csrf
                        <input type="hidden" name="expense_id" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-ok">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalTitle">Invoice Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light" id="view-box">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src='{{ url('/') }}/vendor/bs-custom-file-input.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js"></script>
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).on('click', '.dropdown-menu', function (e) {
            e.stopPropagation();
        });
        $(document).on('click', '.dropdown-item-click', function (e) {
            const element = document.getElementsByClassName("dropdown-menu-drop")[0];
            element.classList.remove("show");
        });
        if ($(window).width() < 992) {
            $('.dropdown-menu a').click(function (e) {
                e.preventDefault();
                if ($(this).next('.submenu').length) {
                    $(this).next('.submenu').toggle();
                }
                $('.dropdown').on('hide.bs.dropdown', function () {
                    $(this).find('.submenu').hide();
                })

            });
        }
        $(document).on('click', '.close-click', function (event) {
            document.getElementById("org_div2").innerHTML = '';
            document.getElementById("org_div1").classList.remove('d-none');
            document.getElementById("org_div1").innerHTML = '';
            // document.getElementById("org_div1").remove();
        });
        $(document).on('click', '.dropdown-item', function (event) {
            event.stopPropagation();
            event.preventDefault();
            const category = $(this).data('category');
            const pid = $(this).data('pid');
            const category2 = $(this).text();

            function drop() {

                // console.log(category);
                const categoryselected = document.getElementsByClassName("category-filter-selected");
                // console.log(categoryselected);
                // console.log(categoryselected);
                document.getElementById("org_div1").classList.add('d-none');
                const rootDiv = document.getElementById("org_div2")
                const elementDiv = document.createElement("div");
                const elementDiv2 = document.createElement("div");
                const elementDiv3 = document.createElement("input");
                // elementDiv.classList.add('custom-select');
                rootDiv.classList.add('input-group');
                elementDiv.classList.add('input-group-prepend');
                elementDiv.classList.add('close-click');
                elementDiv2.classList.add('input-group-text');
                elementDiv3.classList.add('form-control');
                elementDiv3.setAttribute('name', "expense_name");
                elementDiv2.innerHTML = 'X';
                elementDiv.appendChild(elementDiv2)
                rootDiv.appendChild(elementDiv3)
                rootDiv.appendChild(elementDiv);

                // name="expense_name"
                const rootDivv = document.getElementById("org_div1")
                const rootDivvv = document.getElementById("org_div4")
                const elementDivv = document.createElement("option");
                elementDivv.classList.add('category-filter');
                elementDivv.classList.add('category-filter-selected');

                elementDivv.selected = true;
                // elementDiv.attr('selected');
                elementDivv.classList.add('d-none');
                elementDivv.innerHTML = category2;
                elementDivv.value = '_new';
                rootDivvv.value = pid;
                rootDivv.append(elementDivv)


            }

            if (category == undefined) {

            } else if (category == 39) {
                drop();
            } else if (category == 40) {
                drop();
            } else if (category == 41) {
                drop();
            } else {
                const categoryfilter = document.getElementsByClassName("category-filter");
                if (categoryfilter.length > 0) {

                    categoryfilter[0].remove();
                    const rootDiv = document.getElementById("org_div1");
                    const elementDiv = document.createElement("option");
                    elementDiv.classList.add('category-filter');
                    elementDiv.classList.add('category-filter-selected');
                    elementDiv.selected = true;
                    // elementDiv.attr('selected');

                    elementDiv.classList.add('d-none');
                    elementDiv.innerHTML = category2;
                    elementDiv.value = category;
                    rootDiv.append(elementDiv)
                    // var altStr = $( "category-filter" ).attr( "selected");
                } else {
                    const rootDiv = document.getElementById("org_div1")
                    const elementDiv = document.createElement("option");
                    elementDiv.classList.add('category-filter');
                    elementDiv.classList.add('category-filter-selected');

                    elementDiv.selected = true;
                    // elementDiv.attr('selected');
                    elementDiv.classList.add('d-none');
                    elementDiv.innerHTML = category2;
                    elementDiv.value = category;
                    rootDiv.append(elementDiv)
                    // var altStr = $( "category-filter" ).attr( "selected");
                }

            }

            // document.body.append(div);
        });

    </script>

    <script>
        var uploadField = document.getElementById("expense_file");
        uploadField.onchange = function () {
            if (this.files[0].size > 5000000) {
                alert("File is too big");
                this.value = "";
                $('.customSingleFile label[for="expense_file"]').text('Upload File');
            }
            ;
            if (hasExtension('expense_file', ['.exe', '.js'])) {
                alert("File type not allowed");
                this.value = "";
                $('.customSingleFile label[for="expense_file"]').text('Upload File');
            }
        };

        function hasExtension(inputID, exts) {
            var fileName = document.getElementById(inputID).value;
            return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
        }

        function setSettings(key, val) {
            $.ajax({
                url: "{!! route('user-settings/set') !!}",
                processData: true,
                type: 'POST',
                data: {
                    '_token': '{!! csrf_token() !!}',
                    key: key,
                    value: val
                }
            });
        }

        $(document).ready(function () {
            bsCustomFileInput.init();

            @if ($user->getSettingsValue('property_expenses_show_chart') !== '0')
            $('#collapseChart').collapse('show');
            @endif
            $('#collapseChart').on('hide.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('property_expenses_show_chart', '0');
            });
            $('#collapseChart').on('show.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('property_expenses_show_chart', '1');
            });

            @if ($user->getSettingsValue('property_expenses_show_summary') !== '0')
            $('#collapseSummary').collapse('show');
            @endif
            $('#collapseSummary').on('hide.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('property_expenses_show_summary', '0');
            });
            $('#collapseSummary').on('show.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('property_expenses_show_summary', '1');
            });

            var chBar = document.getElementById("chBar");
            var aspectRatio = $(window).width() > 992 ? 4 : 1;
            var chartData = {
                labels: [@foreach($chart as $month => $data) "{{ $data['month'] }}" @if( $data != end($chart)),@endif @endforeach ],
                datasets: [
                    {
                        label: "Income",
                        data: [@foreach($chart as $month => $data) {{ $data['income'] }} @if( $data != end($chart)),@endif @endforeach ],
                        backgroundColor: '#28a745'
                    },
                    {
                        label: "Expenses",
                        data: [@foreach($chart as $month => $data) {{ $data['expenses'] }} @if( $data != end($chart)),@endif @endforeach ],
                        backgroundColor: '#dc3545'
                    },
                    {
                        label: "Net Profit / Loss",
                        data: [@foreach($chart as $month => $data) {{ $data['profit'] }} @if( $data != end($chart)),@endif @endforeach ],
                        backgroundColor: '#4c7fcf'
                    }
                ]
            };
            if (chBar) {
                new Chart(chBar, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        aspectRatio: aspectRatio,
                        scales: {
                            x: {
                                barPercentage: 0.4,
                                categoryPercentage: 0.5
                            },
                            y: {
                                ticks: {
                                    beginAtZero: false,
                                    callback: function (value, index, values) {
                                        if (value >= 0) {
                                            return '$' + value;
                                        } else {
                                            return '-$' + Math.abs(value);
                                        }
                                    }
                                }
                            }
                        },
                        legend: {
                            display: false
                        },

                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var label = context.dataset.label || '';

                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', {
                                                style: 'currency',
                                                currency: 'USD'
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

        });
    </script>

    <script>
        $(document).ready(function () {
            $("#cancelAddBill").click(function (e) {
                e.preventDefault();
                $("#addBillContent").collapse('hide');
            });

            $("#billType").change(function () {
                var val = $(this).val();
                if (val === "_new") {
                    $("#billType").hide();
                    $("#billTypeOtherBox").show();
                    $("#billTypeOther").focus();
                }
            });
            if ($("#billType").val() === "_new") {
                $("#billType").hide();
                $("#billTypeOtherBox").show();
                $("#billTypeOther").focus();
            }
            $("#billTypeCancel").click(function (e) {
                e.preventDefault();
                $("#billType").show();
                $("#billTypeOtherBox").hide();
                $("#billType").val("");
                $("#billTypeOther").val("");
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#editModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                $.post("{{ route('edit-payments') }}", {
                    id: button.data('id'),
                }, function (datajson) {
                    $('#add-box').html(datajson.view);
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('input[name="last12Month"]').change(function () {
                var val = $('input[name="last12Month"]:checked').val();
                if (val == 1) {
                    $('.last12Month1').removeClass('d-none');
                    $('.last12Month0').addClass('d-none');
                } else {
                    $('.last12Month0').removeClass('d-none');
                    $('.last12Month1').addClass('d-none');
                }
            });
            $('#last12Month1').click();

            $('input[name="last12Month-2"]').change(function () {
                var val = $('input[name="last12Month-2"]:checked').val();
                if (val == 1) {
                    $('.last12Month1-2').removeClass('d-none');
                    $('.last12Month0-2').addClass('d-none');
                } else {
                    $('.last12Month0-2').removeClass('d-none');
                    $('.last12Month1-2').addClass('d-none');
                }
            });
            $('#last12Month1-2').click();
        });
    </script>

    <script>
        var expense_id = 0;
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#detailsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                if (button.data('id')) {
                    expense_id = button.data('id');
                }
                var modal = $(this);
                $.post("{{ route('view-expense') }}", {
                    id: expense_id
                }, function (datajson) {
                    $('#view-box').html(datajson.view);
                });
            });
        });
    </script>

    <script>
        function setupAjaxLoadedContent() {
            $(".collapseFilters").click(function (e) {
                e.preventDefault();
                $('.collapseFilters').tooltip('hide');
                var target = $(".filtersRow");
                target.toggleClass("active");
                if (target.hasClass("active")) {
                    $("#advanced_filter").val("1");
                } else {
                    $("#advanced_filter").val("0");
                }
            });
            $('.showViewModal').click(function (e) {
                e.stopPropagation();
                expense_id = $(this).data('id');
                var target = $(this).data('target');
                $(target).modal('show')
            });
            $('.showDeleteRecordModal').click(function (e) {
                e.stopPropagation();
                var target = $(this).data('target');
                $("#confirmDeleteModal").find('.title').text($(this).data('record-title'));
                $("#confirmDeleteModal").find("input[name='expense_id']").val($(this).data('record-id'));
                $(target).modal('show');
            });
        } //function

        jQuery(document).ready(function ($) {
            if (document.getElementById('expensesBox')) {
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id]) }}'
                    + '&r={{rand(10000000,99999999)}}'
                    //+'&parent=property' //by default-all expenses
                    + '&column=expense_date'
                    + '&order=desc';
                $("#expensesBox").load(href, function () {
                    setupAjaxLoadedContent();
                });
            }
            $(document).on("click", '#expensesBox a.page-link, #expensesBox a.sortLink', function (e) {
                e.preventDefault();//column=due_date&order=desc&
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load($(this).attr('href') + "&advanced_filter=" + $("#advanced_filter").val(), function () {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });
            $(document).on("click", '#applyFilters', function (e) {
                e.preventDefault();
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id]) }}'
                    + '&r={{rand(10000000,99999999)}}'
                    + '&advanced_filter=1'
                    + '&column=expense_date'
                    + '&order=desc'
                    + '&expense_date=' + $('#expense_date_field').val()
                    + '&name=' + $('#name_field').val()
                    + '&amount=' + $('#amount_field').val()
                    + '&monthly=' + $('#monthly_field').val()
                    + '&parent=' + $("[name='parent']:checked").val();
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load(href, function () {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });

            $(document).on("change", "[name='parent']", function (e) {
                e.preventDefault();
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id, 'column'=>'expense_date', 'order'=>'desc', 'r'=>rand(10000000,99999999)]) }}'
                    + '&parent=' + $("[name='parent']:checked").val()
                    + '&column=expense_date'
                    + '&order=desc';
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load(href, function () {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });

            $('#monthly').prop('checked', false);
            $('#no_end_date').prop('checked', true);
            $('#endDate').prop('disabled', true);

            $('#monthlyLabel').on('click', function (e) {
                $('#collapseMonthly').collapse('toggle');
            });
            $('#collapseMonthly').on('show.bs.collapse', function (e) {
                if ($('#monthly').is(':checked')) {
                    return false;
                }
            });
            $('#no_end_date').change(function () {
                $('#endDate').val('');
                $('#endDate').prop('disabled', this.checked);
            });
        });
    </script>
@endsection
