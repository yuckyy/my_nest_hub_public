@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('profile') }}">Admin</a> > <a href="#">Delete User</a>
    </div>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Delete User</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session()->get('error') }}
                </div>
            @endif
            <form action="{{ route('delete-user-submit') }}" method="post">
                @csrf

                @if (isset($user))
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                @endif

                <div class="row">
                    <div class="col-sm-4">

                        <div class="card">
                            <div class="card-body alert alert-danger mb-0" style="border-radius: 0">
                                <div class="form-group">
                                    You are about to delete a user
                                </div>
                                <div class="form-group">
                                    <b>{{ $user->full_name }}</b>
                                </div>
                                <div class="form-group">
                                    <b>{{ $user->email }}</b>
                                </div>
                                <div class="form-group">
                                    This operation is not reversible
                                </div>
                            </div>
                            <div class="card-footer mb-0 bg-white">
                                <a href="{{ route('users') }}" class="btn btn-cancel pl-3 pr-3">Cancel</a>
                                <button type="submit" class="btn btn-danger pl-3 pr-3 float-right"><i class="fa fa-trash-alt mr-1"></i> Yes, Delete</button>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

    </script>
@endsection
