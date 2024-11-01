@extends( Auth::check() ? 'layouts.app': 'layouts.public')
@section('content')

    @if (Auth::check())
            <div class="container pt-5">
                <div class="card border-warning propertyForm mb-4">
                    <div class="card-header border-warning text-center alert-warning">
                        <p class="m-0"><i class="fal fa-telescope mr-2"></i> We looked everywhere, but…</p>
                    </div>
                    <div class="card-body text-center alert-warning">
                        <h3 class="m-0">The object isn't here.</h3>
                    </div>
                </div>
            </div>
    @else
            <div class="container-fluid pb-4">
                <div class="container-fluid">
                    <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                        <div class="propCardWrap col-12">
                            <p class="alert alert-warning">
                                Sorry, Page Not Found
                            </p>
                        </div>

                    </div>
                </div>
            </div>
    @endif

@endsection








