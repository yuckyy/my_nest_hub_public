@extends('layouts.auth_app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-8 col-xl-6 pt-3">
                <div class="card">

                    <div class="card-header">
                        <h5 class="m-0">{{ __('Login') }}</h5>
                    </div>

                    <div class="card-body">

                        @if (isset($sessionExpired) && $sessionExpired)
                            <div class="alert text-center alert-warning session__alert" role="alert" >
                                <span>Your session had been expired</span>
                            </div>
                        @endif

                        @if (session('auth'))
                            <div class="alert text-center alert-warning session__alert" role="alert" >
                                <span>Your session had been expired</span>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert text-center alert-warning session__alert" role="alert" >
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif


                        <h6 class="text-center pt-2 pb-3 text-secondary">
                            Don't have an account?
                            <a class="text-primary2" href="{{ route('register') }}?intended_url={{ \Illuminate\Support\Facades\Request::get('intended_url') ?? urlencode(\Illuminate\Support\Facades\Session::get('url.intended')) }}{{ \Illuminate\Support\Facades\Request::get('unit_id') ? "&unit_id".\Illuminate\Support\Facades\Request::get('unit_id') : ""}}">
                                {{ __('Please register') }}
                            </a>
                            or
                        </h6>
                            <h2 class="text-center pt-2 pb-3 text-secondary">

                                <a class="text-primary2" style="text-decoration:none!important" href="{{ route('/google_auth') }}">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a class="text-primary2" style="text-decoration:none!important" href="{{ url('auth/facebook') }}" class="btn btn-facebook">
                                    <i class="fab fa-facebook"></i>
                                </a>

                            </h2>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email">
                                    {{ __('E-Mail Address') }} <i class="required fal fa-asterisk"></i>
                                </label>

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

                            <div class="form-group">
                                <label for="password">
                                    {{ __('Password') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-primary2 float-right" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif

                                <div>
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        autocomplete="current-password"
                                    >

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-group pl-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button type="submit" class="btn btn-primary btn-sm pl-3 pr-4"><i class="fal fa-sign-in mr-1"></i> {{ __('Login') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
