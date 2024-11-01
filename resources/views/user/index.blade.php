@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile') }}">Edit Profile</a>
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
                        <strong>{{ __('Your profile had been changed successfully.') }}</strong>
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

                @include('includes.user.account-menu',['active' => 'profile'])

                <div class="profileNavTabsLeftContent col-md-9">
                    <form method="POST" class="needs-validation checkUnload" novalidate>
                        @csrf

                        <div class="card propertyForm propertyFormGeneralInfo">
                            <div class="card-body bg-light">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName">
                                            First Name <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <input
                                            id="name"
                                            name="name"
                                            type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder=""
                                            value="{{ $user->name }}"
                                            required
                                            autocomplete="name"
                                        >

                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="lastName">
                                            Last Name <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <input
                                            id="lastname"
                                            name="lastname"
                                            type="text"
                                            class="form-control @error('lastname') is-invalid @enderror"
                                            value="{{ $user->lastname }}"
                                            required
                                            autocomplete="lastname"
                                        >

                                        @error('lastname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email">
                                            Email Address <i class="required fal fa-asterisk"></i>
                                        </label>

                                        <input
                                            id="email"
                                            name="email"
                                            type="text"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ $user->email }}"
                                            readonly="readonly"
                                            autocomplete="email"
                                        >
                                        <span class="invalid-feedback" role="alert">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone">
                                            Phone
                                        </label>

                                        <input
                                                id="phone"
                                                name="phone"
                                                type="text"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ $user->phone }}"
                                                maxlength="20"
                                                data-mask="000-000-0000"
                                        >
                                        <span class="invalid-feedback" role="alert">
                                            @error('phone')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

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
