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

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">

                <div class="col-md-9 order-md-last mb-4 mb-md-0">

                    <ul class="nav nav-tabs propertyTabs">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('properties/edit', ['property' => $property->id]) }}">Unit Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('properties/expenses', ['property' => $property->id]) }}">Expenses & Profit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('properties/documents', ['property' => $property->id]) }}">Documents</a>
                        </li>
                        <li class="nav-item mobileActive">
                            <span class="nav-link active" data-href="{{ route('properties/operations', ['property' => $property->id]) }}">Advanced</span>
                        </li>
                    </ul>

                    <div class="card propertyForm">
                        <div class="card-body">
                            <h3>Archive Property</h3>
                            <div class="inRowComment">
                                <i class="fal fa-info-circle"></i> This will archive property, all its units, leases, applications, transactions and maintenance.
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmArchivePropertyModal">
                                <i class="fal fa-file-archive mr-1"></i> Archive
                            </button>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header alert alert-danger text-danger mb-0">
                            <strong>Danger Zone</strong>
                        </div>
                        <div class="card-body">
                            <h3>Delete Property</h3>
                            <div class="inRowComment">
                                <i class="fal fa-info-circle"></i> This will delete property, all its units, leases, applications, transactions and maintenance. <span class="text-danger">This operation cannot be undone.</span>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm mr-3 end-lease-btn" data-toggle="modal" data-target="#confirmDeletePropertyModal">
                                <i class="fal fa-trash-alt mr-1"></i> Delete
                            </button>
                        </div>
                    </div><!-- /propertyForm -->

                </div>

                <div class="col-md-3 order-md-first">
                    @include('properties.edit-photos-partial')
                </div>

            </div>
        </div>

    </div>

    <div class="modal fade" id="confirmArchivePropertyModal" tabindex="-1" role="dialog" aria-labelledby="confirmArchivePropertyModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmArchivePropertyModalTitle">Confirm Archive Property</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>Are you sure you want to archive the property <strong>{{$property->address}}</strong>?<br /> This will archive:</p>
                    <ul>
                        <li>Property and its Units</li>
                        <li>Archive Leases</li>
                        <li>Archive Transactions</li>
                        <li>Archive Maintenance</li>
                        <li>Archive Applications</li>
                    </ul>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> Cancel
                    </button>
                    <form action="{{ route('properties/operations', ['id' => $property->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="archive" value="1">
                        <button class="btn btn-secondary btn-sm mr-3" type="submit">
                            <i class="fal fa-file-archive mr-1"></i> Yes, Archive
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeletePropertyModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeletePropertyModalTitle" aria-hidden="true">
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

@endsection
@section('scripts')
    <script>
        jQuery(document).ready(function () {
        });
    </script>
@endsection
