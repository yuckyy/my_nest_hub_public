@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Expenses & Profit</a>
    </div>

    <div class="container-fluid pb-4">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <div class="p-0 pl-sm-2">
                        <h1 class="h2 d-inline-block fluidHeader">
                            Expenses & Profit
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid unitFormContainer">

            <div class="card propertyForm mb-4">

                <div class="bg-light">
                    <div class="accHeader collapsed card-header border-bottom-0 d-flex justify-content-between" id="headingSummary" data-toggle="collapse" data-target="#collapseSummary" >
                        <div>
                            <i class="fal fa-coins mr-1 h5 mb-0 d-inline-block align-middle"></i> <span class="d-inline-block align-middle">Summary</span>
                        </div>
                        <button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse" data-target="#collapseSummary" aria-expanded="true" aria-controls="collapseSummary" style="margin: -5px 0">
                            <span>Show  <i class="fal fa-eye ml-1"></i></span>
                            <span class="d-none">Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                        </button>
                    </div>
                    <div id="collapseSummary" class="collapse marketingCollapseItem" aria-labelledby="headingSummary">
                        <div class="card-body border-top">
                            <div class="row text-center">
                                <div class="col-md">
                                    <div class="h5">
                                        Income
                                    </div>
                                    <div class="h2 mb-0 text-success last12Month1">
                                        ${{ $totalIncome12 }}
                                    </div>
                                    <div class="h2 mb-0 text-success last12Month0 d-none">
                                        ${{ $totalIncome }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="h5">
                                        Expenses
                                    </div>
                                    <div class="h2 text-danger last12Month1">
                                        ${{ $totalExpenses12 }}
                                    </div>
                                    <div class="h2 text-danger last12Month0 d-none">
                                        ${{ $totalExpenses }}
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="h5">
                                        Net Profit/Loss
                                    </div>
                                    <div class="h2 {{ $totalIncome12 > $totalExpenses12 ? 'text-primary2' : 'text-danger'}} last12Month1">
                                        {{ financeCurrencyFormat($totalIncome12 - $totalExpenses12) }}
                                    </div>
                                    <div class="h2 {{ $totalIncome > $totalExpenses ? 'text-primary2' : 'text-danger'}} last12Month0 d-none">
                                        {{ financeCurrencyFormat($totalIncome - $totalExpenses) }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="last12Month1" value="1" name="last12Month" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="last12Month1">Last 12 Month</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="last12Month0" value="0" name="last12Month" class="custom-control-input">
                                    <label class="custom-control-label" for="last12Month0">View All</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- $sql --}}
                <div class="accHeader collapsed card-header border-top border-bottom-0 d-flex justify-content-between" id="headingChart" data-toggle="collapse" data-target="#collapseChart" >
                    <div>
                        <i class="fal fa-chart-bar mr-1 h5 mb-0 d-inline-block align-middle"></i> <span class="d-inline-block align-middle">Chart</span>
                    </div>
                    <button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse" data-target="#collapseChart" aria-expanded="true" aria-controls="collapseChart" style="margin: -5px 0">
                        <span>Show  <i class="fal fa-eye ml-1"></i></span>
                        <span class="d-none">Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                    </button>
                </div>
                <div id="collapseChart" class="collapse marketingCollapseItem" aria-labelledby="headingChart">
                    <div class="card-body border-top bg-light">
                        <canvas id="chBar"></canvas>
                    </div>
                </div>
            </div>

            <a name="invoices"></a>
            <div id="expensesBox"></div>

            <div class="card propertyForm mb-4">
                <div class="card-header cardHeaderMulti d-sm-flex justify-content-between">
                    <div>Expenses Per Property</div>
                    <div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="last12Month1-2" value="1" name="last12Month-2" class="custom-control-input" checked>
                            <label class="custom-control-label" for="last12Month1-2">Summary For Last 12 Month</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline mr-0">
                            <input type="radio" id="last12Month0-2" value="0" name="last12Month-2" class="custom-control-input">
                            <label class="custom-control-label" for="last12Month0-2">View All</label>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="newFullData">

                    @foreach ($properties as $key => $property)
                        <div class="card unitCard mb-3">
                            <div class="d-block d-sm-table propCardTable">
                                <div class="d-block d-sm-table-row">
                                    <a href="{{ route('properties/expenses', ['property_id' => $property->id]) }}"
                                       class="cardImgSell d-block d-sm-table-cell text-center text-secondary p-0 border-right"
                                       @if($property->imageUrl())
                                           style="background-image: url({{ $property->imageUrl() }});"
                                                @endif
                                        >
                                        <div class="h2">
                                            {!! $property->icon() !!}
                                        </div>
                                    </a>

                                    <div class="cardBodySell d-block d-sm-table-cell">
                                        <div class="cardBody p-2">
                                            <div class="ml-2">
                                                <span class="h5">
                                                    {{ $property->address }},
                                                    {{ $property->city }},
                                                    {{ $property->state->code }},
                                                    {{ $property->zip }}
                                                </span>
                                                @if ($property->status() == 0)
                                                    <span class="badge badge-danger">Occupied</span>
                                                @elseif ($property->status() == 1)
                                                    <span class="badge badge-success">Vacant</span>
                                                @elseif ($property->status() == 2)
                                                    <span class="badge badge-danger mr-1">Occupied</span><span class="badge badge-success">Vacant</span>
                                                @endif
                                            </div>

                                            <div class="propCardSmallText">
                                                <a href="{{ route('properties/expenses', ['$propert_id' => $property->id]) }}" title="Expenses & Profit" class="btn btn-sm btn-light mr-1 text-muted"><i class="fas fa-chart-pie"></i><span> Expenses & Profit</span></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-block d-sm-table-cell">
                                        <div class="last12Month0-2 d-none text-sm-right expensesBox ml-auto mr-4 mb-2">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="d-inline d-lg-block"><small>Income</small></div>
                                                    <div class="d-inline d-lg-block h5 {{ $property->totalIncome() == 0 ? 'text-muted' : 'text-success'}}">${{ $property->totalIncome() }}</div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="d-inline d-lg-block"><small>Expenses</small></div>
                                                    <div class="d-inline d-lg-block h5 {{ $property->totalExpenses() == 0 ? 'text-muted' : 'text-danger'}}">${{ $property->totalExpenses() }}</div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="d-inline d-lg-block"><small>Net Profit/Loss</small></div>
                                                    <div class="d-inline d-lg-block h5 {{ $property->totalIncome() == $property->totalExpenses() ? 'text-muted' : ( $property->totalIncome() > $property->totalExpenses() ? 'text-primary2' : 'text-danger' ) }}">
                                                        {{ financeCurrencyFormat($property->totalIncome() - $property->totalExpenses()) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="last12Month1-2 text-sm-right expensesBox ml-auto mr-4 mb-2">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="d-inline d-lg-block"><small>Income</small></div>
                                                    <div class="d-inline d-lg-block h5 {{ $property->totalIncome12() == 0 ? 'text-muted' : 'text-success'}}">${{ $property->totalIncome12() }}</div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="d-inline d-lg-block"><small>Expenses</small></div>
                                                    <div class="d-inline d-lg-block h5 {{ $property->totalExpenses12() == 0 ? 'text-muted' : 'text-danger'}}">${{ $property->totalExpenses12() }}</div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="d-inline d-lg-block"><small>Net Profit/Loss</small></div>
                                                    <div class="d-inline d-lg-block h5 {{ $property->totalIncome12() == $property->totalExpenses12() ? 'text-muted' : ( $property->totalIncome12() > $property->totalExpenses12() ? 'text-primary2' : 'text-danger' ) }}">
                                                        {{ financeCurrencyFormat($property->totalIncome12() - $property->totalExpenses12()) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="propUnitExpensesBox border-top">
                                <div class="d-block d-sm-table w-100 propCardTable">
                                    @foreach ($property->units as $key => $unit)
                                        <div class="d-block d-sm-table-row bg-light">
                                            <div class="cardImgSell border-top d-none d-sm-table-cell">
                                                @if($unit->imageUrl())
                                                    <a class="photoSuperThumb d-inline-block" href="{{ route('properties/units/expenses', ['unit_id' => $unit->id]) }}"
                                                        style="background-image: url({{ $unit->imageUrl() }});"
                                                    ></a>
                                                @else
                                                    <a href="{{ route('properties/units/expenses', ['unit_id' => $unit->id]) }}">
                                                        <i class="fal fa-door-closed"></i>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="cardBodySell border-top d-block d-sm-table-cell">
                                                <div class="cardBody p-2">
                                                    <div class="ml-2">
                                                        <a class="text-dark" href="{{ route('properties/units/expenses', ['unit_id' => $unit->id]) }}">{{ $unit->name }}</a>
                                                        @if ($unit->isOccupied())
                                                            <span class="badge badge-danger">Occupied</span>
                                                        @else
                                                            <span class="badge badge-success">Vacant</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-block border-top d-sm-table-cell">
                                                <div class="last12Month0-2 d-none text-sm-right expensesBox ml-auto mr-4">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="{{ $unit->totalIncome() == 0 ? 'text-muted' : 'text-success'}}">${{ $unit->totalIncome() }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="{{ $unit->totalExpenses() == 0 ? 'text-muted' : 'text-danger'}}">${{ $unit->totalExpenses() }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="{{ $unit->totalIncome() == $unit->totalExpenses() ? 'text-muted' : ( $unit->totalIncome() > $unit->totalExpenses() ? 'text-primary2' : 'text-danger' ) }}">
                                                                {{ financeCurrencyFormat($unit->totalIncome() - $unit->totalExpenses()) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="last12Month1-2 text-sm-right expensesBox ml-auto mr-4">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="{{ $unit->totalIncome12() == 0 ? 'text-muted' : 'text-success'}}">${{ $unit->totalIncome12() }}</div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="{{ $unit->totalExpenses12() == 0 ? 'text-muted' : 'text-danger'}}">${{ $unit->totalExpenses12() }}</div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="{{ $unit->totalIncome12() == $unit->totalExpenses12() ? 'text-muted' : ( $unit->totalIncome12() > $unit->totalExpenses12() ? 'text-primary2' : 'text-danger' ) }}">
                                                                {{ financeCurrencyFormat($unit->totalIncome12() - $unit->totalExpenses12()) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>


        </div>
    </div>

    {{--
    <!-- DELETE RECORD confirmation dialog-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    --}}

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js"></script>
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function() {
            function setSettings(key, val){
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

            @if ($user->getSettingsValue('expenses_show_chart') !== '0')
                $('#collapseChart').collapse('show');
            @endif
            $('#collapseChart').on('hide.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('expenses_show_chart', '0');
            });
            $('#collapseChart').on('show.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('expenses_show_chart', '1');
            });

            @if ($user->getSettingsValue('expenses_show_summary') !== '0')
                $('#collapseSummary').collapse('show');
            @endif
            $('#collapseSummary').on('hide.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('expenses_show_summary', '0');
            });
            $('#collapseSummary').on('show.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
                setSettings('expenses_show_summary', '1');
            });

            var chBar = document.getElementById("chBar");
            var aspectRatio = $(window).width() > 992 ? 4 : 1;
            var chartData = {
                labels: [ @foreach($chart as $month => $data) "{{ $data['month'] }}" @if( $data != end($chart)),@endif @endforeach ],
                datasets: [
                    {
                        label: "Income",
                        data: [ @foreach($chart as $month => $data) {{ $data['income'] }} @if( $data != end($chart)),@endif @endforeach ],
                        backgroundColor: '#28a745'
                    },
                    {
                        label: "Expenses",
                        data: [ @foreach($chart as $month => $data) {{ $data['expenses'] }} @if( $data != end($chart)),@endif @endforeach ],
                        backgroundColor: '#dc3545'
                    },
                    {
                        label: "Net Profit / Loss",
                        data: [ @foreach($chart as $month => $data) {{ $data['profit'] }} @if( $data != end($chart)),@endif @endforeach ],
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
                                    callback: function(value, index, values) {
                                        if(value >= 0){
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

            $('input[name="last12Month-2"]').change(function(){
                var val = $( 'input[name="last12Month-2"]:checked' ).val();
                if(val == 1){
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
                if(button.data('id')){
                    expense_id = button.data('id');
                }
                var modal = $(this);
                $.post("{{ route('view-expense') }}", {
                    id: expense_id
                }, function(datajson){
                    $('#view-box').html(datajson.view);
                });
            });
        });
    </script>

    {{--}}
    <script>
        function setupAjaxLoadedContent(){
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
            $('.showViewModal').click(function(e){
                e.stopPropagation();
                expense_id = $(this).data('id');
                var target = $(this).data('target');
                $(target).modal('show')
            });
            $('.showDeleteRecordModal').click(function(e){
                e.stopPropagation();
                var target = $(this).data('target');
                $("#confirmDeleteModal").find('.title').text($(this).data('record-title'));
                $("#confirmDeleteModal").find("input[name='expense_id']").val($(this).data('record-id'));
                $(target).modal('show');
            });
        } //function

        jQuery( document ).ready(function($) {
            if(document.getElementById('expensesBox')){
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id]) }}'
                    +'&r={{rand(10000000,99999999)}}'
                    +'&parent=property'
                    +'&column=expense_date'
                    +'&order=desc';
                $("#expensesBox").load(href, function() {
                    setupAjaxLoadedContent();
                });
            }
            $(document).on("click", '#expensesBox a.page-link, #expensesBox a.sortLink', function(e) {
                e.preventDefault();//column=due_date&order=desc&
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load($(this).attr('href')+"&advanced_filter="+$("#advanced_filter").val(), function() {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });
            $(document).on("click", '#applyFilters', function(e) {
                e.preventDefault();
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id]) }}'
                    +'&r={{rand(10000000,99999999)}}'
                    +'&advanced_filter=1'
                    +'&column=expense_date'
                    +'&order=desc'
                    +'&expense_date='+$('#expense_date_field').val()
                    +'&name='+$('#name_field').val()
                    +'&amount='+$('#amount_field').val()
                    +'&monthly='+$('#monthly_field').val()
                    +'&parent='+$("[name='parent']:checked").val();
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load(href, function() {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });

            $(document).on("change", "[name='parent']", function(e) {
                e.preventDefault();
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id, 'column'=>'expense_date', 'order'=>'desc', 'r'=>rand(10000000,99999999)]) }}'
                    +'&parent='+$("[name='parent']:checked").val()
                    +'&column=expense_date'
                    +'&order=desc';
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load(href, function() {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });


            $('#monthly').prop('checked', false);
            $('#no_end_date').prop('checked', true);
            $('#endDate').prop('disabled', true);

            $('#monthlyLabel').on('click', function(e) {
                $('#collapseMonthly').collapse('toggle');
            });
            $('#collapseMonthly').on('show.bs.collapse', function(e) {
                if($('#monthly').is(':checked')) {
                    return false;
                }
            });
            $('#no_end_date').change(function() {
                $('#endDate').val('');
                $('#endDate').prop('disabled', this.checked);
            });

        });
    </script>
    {{--}}

@endsection
