@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/email-preferences') }}">Email Preferences</a>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">My Account</h1>
                    <h6 class="text-center text-sm-left pb-3 text-secondary">
                        <a href="{{ route('profile') }}">{{ Auth::user()->fullName() }}</a>
                    </h6>
                </div>
            </div>

            @if (session('general-error'))
                <div class="customFormAlert alert alert-danger" role="alert">
                    {!! session('general-error') !!}
                </div>
            @endif

        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">

                @include('includes.user.account-menu',['active' => 'email-preferences'])

                <div class="profileNavTabsLeftContent col-md-9">
                    <form method="POST">
                        @csrf
                        <div class="card propertyForm propertyFormGeneralInfo">

                            <div class="card-header">
                                <span>Email Preferences</span>
                            </div>

                            <div class="card-body bg-light">
                                <div class="financeAccountsList mb-3">
                                    @foreach($emailPreferenceList as $pref)
                                        <div class="card financeAccountCard">
                                            <div class="card-body p-2">
                                                <div class="financeAccountCardBody">
                                                    <div class="card-text">
                                                        <div class="card-text">
                                                            {{ $pref['title'] }}
                                                        </div>
                                                        <div class="">
                                                            <span class="mr-2 text-secondary">{{ $pref['description'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="financeAccountCardNav">
                                                    <div class="btn-group align-items-center pr-3">
                                                        <label for="{{ $pref['field'] }}" class="pr-1"><strong>Off</strong></label>
                                                        <div class="custom-control custom-switch yesNoSwitch">
                                                            <input
                                                                    type="checkbox"
                                                                    class="custom-control-input {{ !empty(old($pref['field'])) || !empty($user->preferences->{$pref['field']}) ? "checked-checkbox" : "" }}"
                                                                    id="{{ $pref['field'] }}"
                                                                    name="{{ $pref['field'] }}"
                                                                    value="1"
                                                                    {{ !empty(old($pref['field'])) || !empty($user->preferences->{$pref['field']}) ? "checked" : "" }}
                                                            >
                                                            <label class="custom-control-label" for="{{ $pref['field'] }}"></label>
                                                        </div>
                                                        <label for="{{ $pref['field'] }}"><strong>On</strong></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-footer text-muted">
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
    </script>
@endsection
