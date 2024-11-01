@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')

    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @include('properties.units.header-partial')
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="navTabsLeftContainer col-md-3">
                    @include('includes.units.menu')
                </div>

                <div class="navTabsLeftContent col-md-9">

                    <div class="card propertyForm mb-4">

                        <div class="bg-light">
                            <div class="card-header">
                                Summary
                            </div>
                            <div class="card-body" style="padding: 37px 10px">
                                <div class="row text-center">
                                    <div class="col-md">
                                        <div class="h5 text-success">
                                            Income
                                        </div>
                                        <div class="h2 mb-0 text-success last12Month1">
                                            ${{ $unit->totalIncome12() }}
                                        </div>
                                        <div class="h2 mb-0 text-success last12Month0 d-none">
                                            ${{ $unit->totalIncome() }}
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="h5 text-danger">
                                            Expenses
                                        </div>
                                        <div class="h2 text-danger last12Month1">
                                            ${{ $unit->totalExpenses12() }}
                                        </div>
                                        <div class="h2 text-danger last12Month0 d-none">
                                            ${{ $unit->totalExpenses() }}
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="h5 text-primary2">
                                            Net Profit/Loss
                                        </div>
                                        <div
                                            class="h2 {{ $unit->totalIncome12() > $unit->totalExpenses12() ? 'text-primary2' : 'text-danger'}} last12Month1">
                                            {{ financeCurrencyFormat($unit->totalIncome12() - $unit->totalExpenses12()) }}
                                        </div>
                                        <div
                                            class="h2 {{ $unit->totalIncome() > $unit->totalExpenses() ? 'text-primary2' : 'text-danger'}} last12Month0 d-none">
                                            {{ financeCurrencyFormat($unit->totalIncome() - $unit->totalExpenses()) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
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

                        <div class="card-header border-top">
                            Add Expenses to The Unit
                        </div>
                        <form class="checkUnload" method="POST" action="{{ route('add-expense') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="p-3 bg-light">

                                <!--<div class="inRowComment">
                                    <i class="fal fa-info-circle"></i>
                                    If tenant is registered in MYNESTHUB, our platform will send an email notification to the tenant about added bill. Tenant will also have an ability to view given bill on our platform.
                                </div>-->
                                <input type="hidden" name="unit_id" value="{{ $unit->id }}">
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
                                        <div class="collapse navbar-collapse divdropstyle" id="main_nav">
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

    <!-- Modals -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalTitle">Expense Details</h5>
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
        $(document).ready(function () {
            bsCustomFileInput.init();
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
                var href = '{{ route('ajax-expenses',['unit_id'=>$unit->id]) }}'
                    + '&r={{rand(10000000,99999999)}}'
                    + '&parent=unit'
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
                var href = '{{ route('ajax-expenses',['unit_id'=>$unit->id]) }}'
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
                var href = '{{ route('ajax-expenses',['unit_id'=>$unit->id, 'column'=>'expense_date', 'order'=>'desc', 'r'=>rand(10000000,99999999)]) }}'
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
