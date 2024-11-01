@extends('layouts.auth_app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6 pt-3">
                <div class="card mb-4">

                    <div class="card-header">
                        <h5 class="m-0">{{ __('Create Your Account') }}</h5>
                    </div>

                    @php
                        $intended_url = \Illuminate\Support\Facades\Request::get('intended_url') ?? urlencode(\Illuminate\Support\Facades\Session::get('url.intended'));
                    @endphp
                    <div class="card-body">
                        <h6 class="text-center pt-2 pb-4 text-secondary">
                            Already have a MYNESTHUB account?
                            <a class="text-primary2"
                               href="{{ route('login') }}?intended_url={{ \Illuminate\Support\Facades\Request::get('intended_url') ?? $intended_url }}{{ \Illuminate\Support\Facades\Request::get('unit_id') ? "&unit_id".\Illuminate\Support\Facades\Request::get('unit_id') : ""}}">
                                {{ __(' Please sign in') }}
                            </a>
                        </h6>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Role') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="col-md-6">
                                    <select name="role" class="custom-select fixedMaxInputWidth" id="role">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                    @if (($role->id == 4) && ( strpos(urldecode($intended_url),"role=tenant") > 0 ) ) selected @endif>{{ $role->name }}</option>
                                        @endforeach
                                    </select>

                                    {{--                                <input id="role" type="text" class="form-control @error('role') is-invalid @enderror" name="role" value="{{ old('role') }}" required autocomplete="role" autofocus>--}}

                                    @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">
                                    {{ __('First Name') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="name"
                                        type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        name="name"
                                        value="{{ old('name') }}"
                                        autocomplete="name"
                                        autofocus
                                    >

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="lastname" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Last Name') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="lastname"
                                        type="text"
                                        class="form-control @error('lastname') is-invalid @enderror"
                                        name="lastname"
                                        value="{{ old('lastname') }}"
                                        autocomplete="lastname"
                                        autofocus
                                    >

                                    @error('lastname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">
                                    {{ __('E-Mail Address') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="email"
                                        type="text"
                                        class="form-control @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        autocomplete="email"
                                    >

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Password') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        autocomplete="new-password"

                                        data-toggle="tooltip" data-placement="bottom"
                                        title="Your password must be 12 characters long or more, should contain at-least one letter, one number and one special character."

                                    >

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Confirm Password') }} <i class="required fal fa-asterisk"></i>
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="password-confirm"
                                        type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                    >

                                    @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-end">
                                <div class="col-md-10">
                                    <div
                                        class="custom-control custom-checkbox custom-control-inline pr-4 primary-border-checkbox">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input @error('accept_tos') is-invalid @enderror"
                                            name="accept_tos"
                                            value="1"
                                            id="accept_tos"
                                        >
                                        <label
                                            class="custom-control-label pl-1"
                                            for="accept_tos"
                                        >
                                            By checking this box you agree to <a target="_blank"
                                                                                 href="https://MYNESTHUB.com/terms-of-use.html"
                                                                                 class="text-primary2">Our Terms of
                                                Service</a> and <a target="_blank"
                                                                   href="https://MYNESTHUB.com/privacy-policy.html"
                                                                   class="text-primary2">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {!! NoCaptcha::renderJs() !!}
                            <div class="form-group row {{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }} ">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center" style="">
                                        {!! app('captcha')->display() !!}
                                    </div>
                                    @if ($errors->has('g-recaptcha-response'))
                                        <div class="text-danger text-center">
                                            <strong>Captcha error. Please verify that you are not a robot.</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-sm pl-3 pr-4">
                                        <i class="fal fa-check-circle mr-1"></i> Agree and Continue
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="intended_url"
                                   value="{{ \Illuminate\Support\Facades\Request::get('intended_url') ?? $intended_url }}{{ \Illuminate\Support\Facades\Request::get('unit_id') ? "&unit_id".\Illuminate\Support\Facades\Request::get('unit_id') : ""}}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
