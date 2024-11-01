@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('help/landlord/get-started') }}">Help</a>
    </div>
    <div class="container-fluid p-4">
        <div class="row">
            <div class="helpNavColumn col-md-3 col-lg-2 border-right">
                <a class="h6 mt-0" href="{{ route('help/landlord/get-started') }}">Get Started</a>
                <div class="nav flex-column" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="active mt-2" id="tab-1" data-toggle="pill" href="#content-1" role="tab" aria-controls="content-1" aria-selected="true">Create Property/Unit</a>
                    <a class="" id="tab-2" data-toggle="pill" href="#content-2" role="tab" aria-controls="content-2" aria-selected="false">Move in Tenant</a>
                    <a class="" id="tab-3" data-toggle="pill" href="#content-3" role="tab" aria-controls="content-3" aria-selected="false">View your Payments and add Bills</a>
                    <!--<a class="" id="tab-4" data-toggle="pill" href="#content-4" role="tab" aria-controls="content-4" aria-selected="false">Title 4</a>-->
                </div>
                <a class="h6" href="{{ route('help/landlord/finance') }}">Finance</a>
                <a class="h6" href="{{ route('help/landlord/maintenance') }}">Maintenance</a>

            </div>
            <div class="col-md-9 col-lg-10">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="content-1" role="tabpanel" aria-labelledby="tab-1">
                        <div class="pb-3 text-center text-sm-left">
                            <h1 class="h2">Create Property/Unit</h1>
                        </div>
                        <p>
                            Press "Add New Property" to create a new property.
                        </p>
                        <p class="p-4 bg-light w-75">
                            <img class="w-100 helpImage" src="{{ url('/') }}/images/help/email-verified-create-property.gif">
                        </p>
                        <p>
                            Placeholder content for the tab panel. This one relates to the home tab. Saw you downtown singing the Blues. Watch you circle the drain. Why don't you let me stop by? Heavy is the head that wears the crown. Yes, we make angels cry, raining down on earth from up above. Wanna see the show in 3D, a movie. Do you ever feel, feel so paper thin. It’s a yes or no, no maybe.
                        </p>

                    </div>
                    <div class="tab-pane fade" id="content-2" role="tabpanel" aria-labelledby="tab-2">
                        <div class="pb-3 text-center text-sm-left">
                            <h1 class="h2">Move in tennant</h1>
                        </div>
                        <p>
                            It’s time to move in your tenant! Please watch this little tutorial which will show you how to do that.
                        </p>
                        <p class="p-4 bg-light w-75">
                            <img class="w-100 helpImage" src="{{ url('/') }}/images/help/unit-created-movein-tenant.gif">
                        </p>
                        <p>
                            Placeholder content for the tab panel. This one relates to the home tab. Saw you downtown singing the Blues. Watch you circle the drain. Why don't you let me stop by? Heavy is the head that wears the crown. Yes, we make angels cry, raining down on earth from up above. Wanna see the show in 3D, a movie. Do you ever feel, feel so paper thin. It’s a yes or no, no maybe.
                        </p>
                    </div>
                    <div class="tab-pane fade" id="content-3" role="tabpanel" aria-labelledby="tab-3">
                        <div class="pb-3 text-center text-sm-left">
                            <h1 class="h2">View your Payments and add Bills</h1>
                        </div>
                        <p>
                            You can view your upcoming and paid payments on the “Payments” screen.  There is an ability to create and send rent-specific bills to your client (for example water, gas and etc).                        </p>
                        <p class="p-4 bg-light w-75">
                            <img class="w-100 helpImage" src="{{ url('/') }}/images/help/lease-created-whats-next.gif">
                        </p>
                        <p>
                            Placeholder content for the tab panel. This one relates to the home tab. Saw you downtown singing the Blues. Watch you circle the drain. Why don't you let me stop by? Heavy is the head that wears the crown. Yes, we make angels cry, raining down on earth from up above. Wanna see the show in 3D, a movie. Do you ever feel, feel so paper thin. It’s a yes or no, no maybe.
                        </p>
                    </div>
                    <!--
                    <div class="tab-pane fade" id="content-4" role="tabpanel" aria-labelledby="tab-4">
                        <div class="pb-3 text-center text-sm-left">
                            <h1 class="h2">Title 4</h1>
                        </div>
                        <p>
                            Text4
                        </p>
                        <p class="p-4 bg-light w-75">
                            <img class="w-100 helpImage" src="{{ url('/') }}/images/help/unit-created-movein-tenant.gif">
                        </p>
                        <p>
                            Placeholder 4 content for the tab panel. This one relates to the home tab. Saw you downtown singing the Blues. Watch you circle the drain. Why don't you let me stop by? Heavy is the head that wears the crown. Yes, we make angels cry, raining down on earth from up above. Wanna see the show in 3D, a movie. Do you ever feel, feel so paper thin. It’s a yes or no, no maybe.
                        </p>
                    </div>
                    -->

                </div>








            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        jQuery(document).ready(function($){
        });
    </script>
@endsection