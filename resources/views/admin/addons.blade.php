@extends('layouts.app')

@section('content')
    <style media="screen">
        thead tr:last-child{display: none;}
    </style>
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('profile') }}">Admin</a> > <a href="#">Addons</a>
    </div>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Addons</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="table-responsive-xl">
                <table class="table table-sm table-hover">

                @foreach( $addons as $addon)
                    <tr>
                        <th>{{ $addon->title }}</th>
                        <td>{{ $addon->description }}</td>
                        <td>${{ $addon->price }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin/addons.edit', ['addon' => $addon->id]) }}" title="Edit Addon" class="btn btn-sm btn-primary">
                                <i class="fa fa-pen"></i> Edit Addon
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        jQuery( document ).ready(function($) {
        });
    </script>
@endsection
