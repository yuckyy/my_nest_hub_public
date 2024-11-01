@extends('layouts.auth_app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6 pt-3">
            <div class="card">
                <div class="card-header"><h5 class="m-0">You have been unsubscribed from all email notifications</h5></div>

                <div class="card-body">
                    <div class="text-center">
                        You may subscribe again and manage your email preferences by <a class="text-primary2" href="{{ route('profile/email-preferences') }}">Clicking Here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
