@extends('layouts.app')

@section('content')
    <style media="screen">
        thead tr:last-child{display: none;}
    </style>
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('profile') }}">Admin</a> > <a href="#">Product Prices</a>
    </div>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Product Prices</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="table-responsive-xl">
                <table class="table table-sm table-hover">

                <tr>
                    <th>&nbsp;</th>
                    <th>Price</th>
                    <th>Max Units count</th>
                    @foreach($subscriptionOptions as $option)
                        <th>
                            {{ $option->name }} <a href="#editOptionModal" class="text-primary2 ml-1" title="Edit Option Name" data-toggle="modal" data-target="#editOptionModal" data-option-id="{{ $option->id }}" data-option-name="{{ $option->name }}"><i class="fa fa-pen"></i></a>
                        </th>
                    @endforeach
                    <th class="text-right">
                        <a href="#addOptionModal" data-toggle="modal" data-target="#addOptionModal" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Option</a>
                    </th>
                </tr>

                @foreach( $plans as $plan)
                    <tr>
                        <th>{{ $plan->name }}</th>
                        <td>${{ $plan->price }}</td>
                        <td>{{ $plan->max_units }}</td>
                        @foreach($subscriptionOptions as $option)
                            <td>
                                {!! $plan->hasOption($option->id) ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}
                            </td>
                        @endforeach
                        <td class="text-right">
                            <a href="{{ route('products.edit', ['product' => $plan->id]) }}" title="Edit Product" class="btn btn-sm btn-primary">
                                <i class="fa fa-pen"></i> Edit Plan
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        </div>
    </div>

    <!-- EDIT OPTION dialog-->
    <div class="modal fade" id="editOptionModal" tabindex="-1" role="dialog" aria-labelledby="editOptionModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOptionModalTitle">Edit Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('save-subscription-option') }}" method="post">
                    @csrf
                    <input type="hidden" id="option_id" name="option_id" value="0">
                    <div class="modal-body bg-light">
                        <div id="edit-bank-form">
                            <div class="form-group">
                                <label for="optionName">
                                    Option Name
                                </label>
                                <input class="form-control" id="optionName" type="text" name="option_name">
                            </div>

                            <div class="form-group pt-2">
                                <label for="optionDelete">
                                    Delete This Option
                                </label>
                                <input class="" id="optionDelete" type="checkbox" name="option_delete" value="delete">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ADD OPTION dialog-->
    <div class="modal fade" id="addOptionModal" tabindex="-1" role="dialog" aria-labelledby="addOptionModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOptionModalTitle">Add Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('save-subscription-option') }}" method="post">
                    @csrf
                    <input type="hidden" id="option_id" name="option_id" value="0">
                    <div class="modal-body bg-light">
                        <div id="edit-bank-form">
                            <div class="form-group">
                                <label for="optionName">
                                    Option Name
                                </label>
                                <input class="form-control" id="optionName" type="text" name="option_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        jQuery( document ).ready(function($) {
            $('#editOptionModal').on('show.bs.modal', function (event) {
                var t = $(event.relatedTarget);
                $('#option_id').val(t.data('option-id'));
                $('#optionName').val(t.data('option-name'));

                $('#optionDelete').prop("checked", false);
            });
        });
    </script>

@endsection
