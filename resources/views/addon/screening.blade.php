@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('applications') }}">Applications</a> > <a href="#">Screening</a>
    </div>
    <div class="container pb-4">
        <div class="row justify-content-md-center">
            <div class="col-lg-8">
                <div class="container">
                    <div class="pt-4 pb-3">
                        <h1 class="h2 text-center text-sm-left">Send Screening Request</h1>
                    </div>
                </div>
                <div class="container">
                    <form class="needs-validation" method="post" action="{{ route('addon/screening/send') }}" id="share-application" novalidate>
                        @csrf
                        <input type="hidden" name="application_id" value="{{ $application->id }}">

                        <div class="card propertyForm">
                            <div class="card-header">
                                <i class="fal fa-shield-alt mr-2"></i> Send Screening Request
                            </div>

                            <div class="card-body bg-light">
                                <div class="">
                                    You are about to send a screening request to the Tenant Report about <strong>{{ $application->firstname }} {{ $application->lastname }}</strong>.
                                    Your potential tenant will have to fill his data into the Tenant Report request form and pay $30 to screening service provider.
                                </div>

                            </div>

                            <div class="card-footer text-muted">
                                <a href="{{ route('applications') }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>

                                <button
                                    type="submit"
                                    role="submit"
                                    class="btn btn-primary btn-sm float-right"
                                >
                                    <i class="fal fa-check-circle mr-1"></i> Send
                                </button>
                            </div>
                        </div><!-- /propertyForm -->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
