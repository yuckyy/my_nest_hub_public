@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Request new feature</a>
    </div>
    <div class="container pb-4">
        <div class="row justify-content-md-center">
            <div class="col-lg-8">
                <div class="container">
                    <div class="pt-4 pb-3">
                        <h1 class="h2 text-center text-sm-left">Request New Feature</h1>
                    </div>
                </div>
                <div class="container">
                    <form class="needs-validation" method="post" action="{{ route('dashboard/request-feature-save') }}"
                          id="share-application" novalidate>
                        @csrf

                        <div class="card propertyForm">
                            <div class="card-header">
                                <i class="fal fa-lightbulb-on mr-2"></i> Share Your Ideas!
                            </div>

                            <div class="card-body bg-light">

                                <div class="alert alert-primary">
                                    <p>If you have an idea for a feature that you'd like to see in the MYNESTHUB
                                        Property Management System, you can submit a request here. Also please report
                                        any bugs or typos you see.</p>
                                    <p class="p-0 m-0">Thank you all for being part of our journey and for helping us
                                        make MYNESTHUB you need it to be!</p>
                                </div>

                                <label for="addEmpName">Your Request <i class="required fal fa-asterisk"></i></label>

                                <textarea
                                    class="form-control @error('request') is-invalid @enderror"
                                    name="request"
                                    required="required"
                                >{{ old('request') }}</textarea>

                                <span class="invalid-feedback" role="alert">
                                    @error('request')
                                    {{ $message }}
                                    @enderror
                                </span>

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
                                    <i class="fal fa-check-circle mr-1"></i> Submit
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
    <script src='{{ asset('js/validation.js') }}'></script>
@endsection
