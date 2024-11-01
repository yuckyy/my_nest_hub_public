<div class="text-center text-sm-left">
    @if(!empty($unit))
    <h1 class="h2 d-inline-block fluidHeader">
        {{--<span class="text-secondary">Unit 1.</span>--}}
        {{ $unit->name }}
    </h1>

    @if (!Auth::user()->isTenant())
        @if ($unit->isOccupied())
        <span class="badge badge-danger align-top">Occupied</span>
        @else
        <span class="badge badge-success align-top">Vacant</span>
        @endif
    @endif

    <h6 class="text-center text-sm-left pb-3 fluidHeader">
        {{ $unit->property->address }},
        {{ $unit->property->city }},
        {{ $unit->property->state->code }},
        {{ $unit->property->zip }}
    </h6>
    @endif
</div>
