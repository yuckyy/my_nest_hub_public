@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/membership') }}">Dwola</a>
    </div>
    <div class="container pb-4">
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
        <div class="row justify-content-md-center">
            <div class="col-lg-12">
                <div class="container">
                    <div class="pt-4 pb-3">
                        <h1 class="h2 text-center text-sm-left">Dwola</h1>
                    </div>
                </div>
                <div class="container">
                    <div class="card propertyForm">
                        <div class="card-header">
                            <i class="fal fa-credit-card mr-2"></i> Connect Bank Account
                        </div>

                        <div class="card-body bg-light">

                            <div id="mainContainer">
                                <input type="button" id="start" value="Add Bank" />
                            </div>

                            <div id="iavContainer"></div>

                        </div>

                        <div class="card-footer text-muted">

                        </div>
                    </div><!-- /propertyForm -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.dwolla.com/1/dwolla.js"></script>
    <script type="text/javascript">
        $('#start').click(function() {
            var iavToken = '{{ $token }}';
            dwolla.configure('sandbox');
            dwolla.iav.start(iavToken, {
                container: 'iavContainer',
                stylesheets: [
                    'http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext'
                ],
                microDeposits: false,
                fallbackToMicroDeposits: false
            }, function(err, res) {
                //handle errors
                console.log('Error: ' + JSON.stringify(err) + ' -- Response: ' + JSON.stringify(res));
            });
        });
    </script>



    <script>
        $(document).ready(function() {
        });
    </script>
@endsection
