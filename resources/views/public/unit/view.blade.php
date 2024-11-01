@extends('layouts.public')

@section('content')
    <style>

    </style>

    <div class="container-fluid pl-md-5 pr-md-5 pt-4 pb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="pb-2">
                    <div class="d-inline-block pb-2 pr-2">
                        @if ($unit->property->type)
                            <div class="propertyMarker">{!! $unit->property->icon() !!} {{ $unit->property->type->name }}</div>
                            @if ($unit->isOccupied())
                                <span class="propertyMarker occupied">Occupied</span>
                            @else
                                <span class="propertyMarker vacant">Vacant</span>
                            @endif
                        @endif
                    </div>
                    <h1 class="h3 d-inline-block mb-0 align-middle text-primary2">{{ $unit->name }}</h1>
                </div>
                <div class="h6 pb-2 pl-1"><i class="fas fa-map-marker-alt mr-1 text-primary2"></i> {{ $unit->property->full_address }}</div>
            </div>
            @if($unit->available_date)
                <div class="col-md-2 pb-3">
                    <div class="bg-light text-center p-2">
                        <div class="text-muted">Available Date</div>
                        <div><strong>{{ Carbon\Carbon::parse($unit->available_date)->format("M d, Y") }}</strong></div>
                    </div>
                </div>
            @else
                <div class="col-md-2">
                </div>
            @endif
            <div class="col-md-4 text-center text-md-right pb-3">
                <a href="{{ env('APP_URL').'applications/add?unit_id=' . $unit->id . '&role=tenant'}}" class="btn btn-success btn-lg"><i class="fal fa-check mr-1"></i> Apply Now</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @php( $i = 0 )
                        @if ($unit->imageUrl())
                            <li data-target="#imageCarousel" data-slide-to="0" class="active"></li>
                        @endif
                        @foreach ($unit->gallery as $key => $gallery)
                            @php( $i++ )
                            <li data-target="#imageCarousel" data-slide-to="{{ $i }}"></li>
                        @endforeach
                        @if ($unit->property->imageUrl())
                            @php( $i++ )
                            <li data-target="#imageCarousel" data-slide-to="{{ $i }}"></li>
                        @endif
                        @foreach ($unit->property->gallery as $key => $gallery)
                            @php( $i++ )
                            <li data-target="#imageCarousel" data-slide-to="{{ $i }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @if ($unit->imageUrl())
                            <div class="carousel-item active" style="background-image: url({{ $unit->imageUrl() }})"></div>
                        @endif
                        @foreach ($unit->gallery as $key => $gallery)
                            <div class="carousel-item" style="background-image: url({{ url('storage/property/' . $unit->property->id . '/' . $unit->id . '/gallery/' . $gallery->filename) }})"></div>
                        @endforeach
                        @if ($unit->property->imageUrl())
                            <div class="carousel-item" style="background-image: url({{ $unit->property->imageUrl() }})"></div>
                        @endif
                        @foreach ($unit->property->gallery as $key => $gallery)
                            <div class="carousel-item" style="background-image: url({{ url('storage/property/' . $unit->property->id . '/gallery/' . $gallery->filename) }})"></div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 frontMapColumn">
                <iframe
                        width="600"
                        height="600"
                        frameborder="0" style="border:0"
                        src="https://www.google.com/maps/embed/v1/place?key={{ env('GOOGLE_MAP_API_KEY') }}&q={{ urlencode( $unit->property->address.",".$unit->property->city.",". ($unit->property->state ? $unit->property->state->code . "," : "").$unit->property->zip) }}" allowfullscreen>
                </iframe>
                <div class="publicUnitAmenities mt-3">
                    <div><i class="fas fa-vector-square"></i><span> Square: {{ $unit->square }} Sq. Ft.</span></div>
                    <div><i class="fas fa-bed"></i><span> Bedrooms: {{ $unit->bedrooms }}</span></div>
                    @if ($unit->full_bathrooms)
                        <div><i class="fas fa-bath"></i><span> Full bathrooms: {{ $unit->full_bathrooms }}</span></div>
                    @endif
                    @if ($unit->half_bathrooms)
                        <div><i class="fas fa-sink"></i><span> Half bathrooms: {{ $unit->half_bathrooms }}</span></div>
                    @endif
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                @if($unit->monthly_rent)
                    <div class="col-md pt-3">
                        <h4 class="text-primary2">Monthly Rent: <strong>{!! financeCurrencyFormat($unit->monthly_rent) !!}</strong></h4>
                    </div>
                @endif

                @if($unit->security_deposit)
                    <div class="col-md pt-3">
                        <h4 class="text-secondary text-lg-right">Security Deposit: <strong>{!! financeCurrencyFormat($unit->security_deposit) !!}</strong></h4>
                    </div>
                @endif
                </div>
            </div>
        </div>

        <div class="pt-3">
            <h2 class="text-secondary">Description</h2>
            <p>{!! str_replace("\n", '', $unit->description) !!}</p>
        </div>

        <div class="p-4 mt-4 bg-light">
            <div class="frontAmenities">
                @foreach ($structures as $parent_s)
                        {{--@if ($parent_s->name <> '')
                            <p class="h6">
                                <i class="fal {{ $parent_s->icon }} mr-2"></i>
                                {{ $parent_s->name }}
                            </p>
                        @endif--}}
                        @foreach ($parent_s->children() as $s)
                            @if (($parent_s->group_type === 'radio') || ($parent_s->group_type === 'checkbox'))
                                @if ($s->group_type === 'textarea')
                                    @if ($s->value <> '')
                                        <div>
                                            {{ $s->value }}
                                        </div>
                                    @endif
                                @else
                                    <div class="d-table">
                                        <div class="d-table-row">
                                            <div class="d-table-cell">
                                                @if (($s->value == 'checked') && ($s->name != "DON'T SPECIFY"))
                                                    <div>
                                                        @if ( $s->icon )<i class="fas {{ $s->icon }}"></i>@endif<span class="frontAmenity">{{ ucfirst(strtolower($s->name)) }}</span>
                                                    </div>
                                                @endif

                                                <div class="ml-4">
                                                    @foreach ($s->children() as $sub_s)
                                                        @if ($sub_s->value == 'checked')
                                                            <div>
                                                                @if ( $sub_s->icon )<i class="fas {{ $sub_s->icon }}"></i>@endif<span class="frontAmenity">{{ ucfirst(strtolower($sub_s->name)) }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if ($parent_s->group_type === 'textarea')
                                @if ($s->value <> '')
                                    <div>
                                        {{ $s->value }}
                                    </div>
                                @endif
                            @endif

                        @endforeach
                @endforeach
            </div>
        </div>

        @if($unit->additional_requirements || $unit->minimum_credit || $unit->minimum_income)
            <div class="pt-3">
                <h2 class="text-secondary">Requirements</h2>
                <div class="border">
                @if($unit->minimum_credit || $unit->minimum_income)
                    <div class="row text-center">
                    @if($unit->minimum_credit)
                        <div class="col @if($unit->minimum_income) border-right @endif">
                            <div class="p-2">
                            Minimum Credit: {!! financeCurrencyFormat($unit->minimum_credit) !!}
                            </div>
                        </div>
                    @endif
                    @if($unit->minimum_income)
                        <div class="col">
                            <div class="p-2">
                            Minimum Income: {!! financeCurrencyFormat($unit->minimum_income) !!}
                            </div>
                        </div>
                    @endif
                    </div>
                    <div class=" @if($unit->additional_requirements) border-bottom @endif"></div>
                @endif
                @if($unit->additional_requirements)
                    <div class="row">
                        <div class="col">
                            <div class="p-2 text-center">
                                {!! str_replace("\n", '', $unit->additional_requirements) !!}
                            </div>
                        </div>
                    </div>
                @endif
                </div>

            </div>
        @endif

    </div>
@endsection
@section('scripts')
    <script>
        jQuery(document).ready(function () {
            $('.carousel').carousel()
        });
    </script>
@endsection
