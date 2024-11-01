@extends('layouts.auth_app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6 pt-3">
                <div class="card">
                    <div class="card-header d-block d-sm-flex">
                        <h5 class="m-0">{{ __('passwords.reset.password') }}</h5>
                        <a class="btn btn-default btn-sm text-muted" style="margin: -0.1rem 0 -0.5rem auto;" href="{{ route('login') }}"><i class="fal fa-arrow-square-left mr-1"></i> Back to Sign in</a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success text-center" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h6 class="text-center pt-2 pb-4 text-secondary">
                            We’ll send you a link to sign in and reset your password.
                        </h6>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="d-sm-flex align-items-start justify-content-center pb-4">
                                <label for="email" class="pr-3 pt-2">
                                    {{ __('E-Mail Address') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="">
                                    <input
                                        id="email"
                                        type="text"
                                        class="form-control @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        autocomplete="email"
                                        autofocus
                                    >

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-0 text-center pt-1 pb-2">
                                <button type="submit" class="btn btn-primary btn-sm pl-3 pr-4">
                                    <i class="fal fa-check-circle mr-1"></i> {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
