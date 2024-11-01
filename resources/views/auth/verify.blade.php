@extends('layouts.auth_app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6 pt-3">
            <div class="card">
                <div class="card-header"><h5 class="m-0">{{ __('Verify Your Email Address') }}</h5></div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('New verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Signup almost complete!') }}
                    {{ __('Please check your inbox for your account activation email. You won\'t be able to proceed without activating your account.') }}
                    {{-- {{ __('Before proceeding, please check your email for a verification link.') }} --}}
                    {{ __('If you did not receive the email') }}, <a class="text-primary2" href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
                    <div class="pt-3">
                        <button class="btn btn-primary" type="button" onclick="event.preventDefault();$('#logout-form').submit();"><i class="fal fa-arrow-square-left mr-1"></i> Back to Login</button>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
