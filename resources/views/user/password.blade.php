@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/password') }}">Change Password</a>
    </div>

    <div class="container-fluid pb-4">
        <div class="container-fluid">
            <div class="d-block d-sm-flex flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">My Account</h1>
                    <h6 class="text-center text-sm-left pb-3 text-secondary">
                        <a href="{{ route('profile') }}">{{ Auth::user()->fullName() }}</a>
                    </h6>
                </div>

                <div class="col-1"></div>

                @if($success)
                    <div class="col-3 text-success">
                        <strong>{{ __('Your password had been changed successfully.') }}</strong>
                    </div>
                @endif
                {{-- <div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                    <a href="#" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                    <a href="#" class="btn btn-primary btn-sm"><i class="fal fa-check-circle mr-1"></i> Save</a>
                </div> --}}
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">

                @include('includes.user.account-menu',['active' => 'password'])

                <div class="profileNavTabsLeftContent col-md-9">
                    <form class="needs-validation" novalidate method="POST">
                        @csrf
                        <div class="card propertyForm propertyFormGeneralInfo">
                            <div class="card-body bg-light">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="currentPassword">
                                            Type Your Current Password Here<i class="required fal fa-asterisk"></i>
                                        </label>

                                        <div class="input-group showHidePassword">
                                            <input
                                                name="current_password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                type="password"
                                                id="currentPassword"
                                                required
                                            >

                                            <div class="input-group-append">
                                                <button class="btn btn-grey" type="button">
                                                    <i class="fal fa-eye" aria-hidden="true"></i>
                                                </button>
                                            </div>

                                            @error('current_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="new_password">
                                            New Password <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <div class="input-group showHidePassword">
                                            <input
                                                name="new_password"
                                                type="password"
                                                class="form-control @error('new_password') is-invalid @enderror"
                                                id="new_password"
                                                required
                                            >

                                            <div class="input-group-append">
                                                <button class="btn btn-grey" type="button"><i class="fal fa-eye" aria-hidden="true"></i></button>
                                            </div>

                                            @error('new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password">
                                            Confirm Password <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <div class="input-group showHidePassword">
                                            <input
                                                name="confirm_password"
                                                type="password"
                                                class="form-control @error('confirm_password') is-invalid @enderror"
                                                id="confirm_password"
                                                required
                                            >

                                            <div class="input-group-append">
                                                <button class="btn btn-grey" type="button">
                                                    <i class="fal fa-eye" aria-hidden="true"></i>
                                                </button>
                                            </div>

                                            @error('confirm_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="inRowComment">
                                    <i class="fal fa-info-circle"></i>
                                    &nbsp;Please use password at least 8 characters long, with letters and numbers.
                                </div>
                            </div>

                            <div class="card-footer text-muted">
                                <a href="{{ route("dashboard") }}" class="btn btn-cancel btn-sm mr-3 d-none d-sm-inline-block">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm float-right">
                                    <i class="fal fa-check-circle mr-1"></i> Save
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(".showHidePassword").each(function(){
            $(this).find("button").on('click', function(event) {
                event.preventDefault();
                var elm = $(this).parent().parent();
                if(elm.find('input').attr("type") === "text"){
                    elm.find('input').attr('type', 'password');
                    elm.find('i').addClass("fa-eye").removeClass("fa-eye-slash");
                } else {
                    elm.find('input').attr('type', 'text');
                    elm.find('i').removeClass("fa-eye").addClass("fa-eye-slash");
                }
            });
        });
    </script>
@endsection
