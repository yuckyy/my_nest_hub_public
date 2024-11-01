@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('applications') }}">Applications</a> > <a href="#">Share Application</a>
    </div>
    <div class="container pb-4">
        <div class="row justify-content-md-center">
            <div class="col-lg-8">
                <div class="container">
                    <div class="pt-4 pb-3">
                        <h1 class="h2 text-center text-sm-left">Share Application</h1>
                    </div>
                </div>
                <div class="container">
                    <form class="needs-validation" method="post" action="{{ route('applications/share/post', ['id' => $application->id]) }}" id="share-application" novalidate>
                        @csrf
                        @if (!empty(Request::get('unit_id')))
                        <input type="hidden" name="unit_id" value="{{ Request::get('unit_id') }}">
                        @endif

                        <div class="card propertyForm">
                            <div class="card-header">
                                <i class="fal fa-share-alt mr-2"></i> Share Application
                            </div>

                            <div class="card-body bg-light">
                                <div class="inRowComment">
                                    If user is not in our system, don’t worry, we will invite them to use our system
                                </div>

                                <label for="addEmpName">Email <i class="required fal fa-asterisk"></i></label>

                                <input
                                    type="text"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email') }}"
                                >

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="card-footer text-muted">
                                <a href="{{ url()->previous() }}" class="btn btn-cancel btn-sm mr-3">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>

                                <button
                                    type="submit"
                                    role="submit"
                                    class="btn btn-primary btn-sm float-right"
                                >
                                    <i class="fal fa-check-circle mr-1"></i> Share Application
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
