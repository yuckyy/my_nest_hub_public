@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('properties') }}">Properties</a> > <a href="#">Archive</a>
    </div>
    <div class="container-fluid">


        <div class="container-fluid">

            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Archived Properties</h1>
                    <span class="badge badge-dark align-top">{{ count($properties) }} total</span>
                </div>
                <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                    {{--}}
                    <button
                        data-toggle="tooltip"
                        data-placement="top"
                        title="List view"
                        class="btn btn-sm btn-light mr-1 d-none d-md-inline-block"
                        type="button"
                        id="buttonListSw"
                    >
                        <i class="fal fa-th-list"></i>
                    </button>
                    {{--}}

                    <a href="{{ route('properties') }}" class="btn btn-outline-secondary btn-sm mr-3"><i class="fal fa-times mr-1"></i> Exit Archive</a>

                    {{--}}
                    <form method="GET" class="input-group input-group-sm mr-3">
                        <input
                            name="address"
                            value="{{ \Request::has('address') ? \Request::get('address') : '' }}"
                            type="text"
                            class="form-control"
                            placeholder="Search by address"
                            aria-label="Search by address"
                            aria-describedby="button-addon2"
                        >
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="button-addon2" onclick="location.href='{{ route('properties') }}';">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </form>
                    {{--}}
                </div>
            </div>

        </div>

        <div class="container-fluid">
            <div id="propsBox" class="row cardsBox">
                @if (count($properties) === 0)
                    <div class="propCardWrap col-12">
                        <p class="alert alert-info">
                            No properties found.
                        </p>
                    </div>
                @endif

                @foreach ($properties as $property)
                    <div class="propCardWrap col-lg-6">
                        <div class="propCard">
                            <div class="d-block d-sm-table propCardTable">
                                <div class="d-block d-sm-table-row">
                                    <div
                                        href="#"
                                        class="cardImgSell d-block d-sm-table-cell text-center text-secondary p-3"
                                        @if($property->imageUrl())
                                            style="background-image: url({{ $property->imageUrl() }});"
                                        @endif
                                    >
                                        <div class="display-4">
                                            @if (!$property->image)
                                                {!! $property->icon() !!}
                                            @endif
                                        </div>

                                        @if ($property->type)
                                            <div class="h6">
                                                <strong>{{ $property->type->name }}</strong><br />
                                                @php( $unitCount = $property->units->count() )
                                                {{ $unitCount . ' ' . Str::plural('unit', $unitCount) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="cardBodySell d-block d-sm-table-cell">
                                        <div class="cardBody">
                                            <h5 class="ml-2 card-title">
                                                {{ $property->address ?? 'Edit Property' }}
                                            </h5>

                                            <p class="ml-2 card-text">
                                                {{ $property->city }},
                                                @if ($property->state)
                                                    {{ $property->state->code }},
                                                @endif
                                                {{ $property->zip }}
                                            </p>

                                            <p class="card-text propCardSmallText">
                                                <span >
                                                    <button data-toggle="modal" data-target="#confirmUnArchiveModal" data-record-id="{{ $property->id }}" data-record-title="{{ '#' . $property->address }}"  class="btn btn-sm btn-light text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Unarchive Property"><i class="fal fa-box-open"></i> Unarchive Property</button>
                                                    <button type="button" class="btn btn-danger btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmDeletePropertyModal2">
                                                        <i class="fal fa-trash-alt mr-1"></i> Delete
                                                    </button>
                                                </span>
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="confirmDeletePropertyModal2" tabindex="-1" role="dialog" aria-labelledby="confirmDeletePropertyModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeletePropertyModalTitle">Confirm Delete Property</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body bg-light">
                                <p>Are you sure you want to delete the property <strong>{{$property->address}}</strong>?<br /> This will delete:</p>
                                <ul>
                                    <li>Property and its Units</li>
                                    <li>Delete Leases</li>
                                    <li>Delete Transactions</li>
                                    <li>Delete Maintenance</li>
                                    <li>Delete Applications</li>
                                </ul>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </button>
                                <form action="{{ route('properties/operations', ['id' => $property->id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="delete" value="1">
                                    <button class="btn btn-danger btn-sm mr-3" type="submit">
                                        <i class="fal fa-trash-alt mr-1"></i> Yes, Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>

            @if (count($properties) > 20)
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-caret-left"></i></a></li>
                        @for ($i = 1; $i < count($properties / 20) + 1; $i++)
                            <li class="page-item{{ $i === $page ? ' active' : ''}}">
                                <a class="page-link" href="#">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-caret-right"></i></a></li>
                    </ul>
                </nav>
            @endif

        </div>

    </div>

    <!-- UNARCHIVE RECORD -->
    <div class="modal fade" id="confirmUnArchiveModal" tabindex="-1" role="dialog" aria-labelledby="confirmUnArchiveModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUnArchiveModalTitle">Confirm Unarchive</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div>Are you sure you would like to unarchive <b><i class="title"></i></b>?</div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary btn-ok"><i class="fal fa-box-open mr-1"></i> Unarchive</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function setListView(){
            localStorage.view="listView";
            $("#propsBox").addClass("listView");
            var obj = $("#buttonListSw");
            obj.find("i").addClass("fa-th-large");
            obj.find("i").removeClass("fa-th-list");
            obj.attr("title", "Grid view");
            obj.attr("data-original-title", "Grid view");
            obj.tooltip('hide');
            $('.propCardSmallText').find("a").tooltip('enable');
        }

        function setGreedView(){
            localStorage.view="greedView";
            $("#propsBox").removeClass("listView");
            var obj = $("#buttonListSw");
            obj.find("i").removeClass("fa-th-large");
            obj.find("i").addClass("fa-th-list");
            obj.attr("title", "List view");
            obj.attr("data-original-title", "List view");
            obj.tooltip('hide');
            $('.propCardSmallText').find("a").tooltip('disable');
        }

        $( document ).ready(function() {
            $('.propCardSmallText').find("a").tooltip('disable');

            $("#buttonListSw").click(function(){
                $("#propsBox").toggleClass("listView");
                if($("#propsBox").hasClass("listView")){
                    setListView();
                } else {
                    setGreedView();
                }
                $("#buttonListSw").tooltip('show');
            });

            if(localStorage.view === "listView"){
                setListView();
            }

            // UNARCHIVE RECORD
            $('#confirmUnArchiveModal').on('click', '.btn-ok', function(e) {
                var id = $(this).data('record-id');

                var form_data = new FormData();
                form_data.append("record_id", id);
                form_data.append("_token", '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('ajax_property_unarchive') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        window.location.reload(true);
                        console.log(response);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });
            $('#confirmUnArchiveModal').on('show.bs.modal', function(event) {
                var t = $(event.relatedTarget);
                $(this).find('.title').text(t.data('record-title'));
                $(this).find('.btn-ok').data('record-id', t.data('record-id'));
            });

        });
    </script>
@endsection
