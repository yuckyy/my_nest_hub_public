<div class="text-center text-sm-left">
    @if(!empty($unit))
        <div class="p-0 pl-sm-2">
            <h1 class="h2 d-inline-block fluidHeader">
                {{ $unit->name }}
            </h1>
            @if ($unit->isOccupied())
                <span class="badge badge-danger align-top">Occupied</span>
            @else
                <span class="badge badge-success align-top">Vacant</span>
            @endif
        </div>
        <div class="text-center text-sm-left pb-3 fluidHeader">
            <a href="{{ route("properties/edit",['id'=>$unit->property_id]) }}" class="btn btn-light" data-toggle="tooltip" data-placement="top" title="Back to Edit Property" >
                <i class="fal fa-arrow-left mr-1 text-muted"></i>
                {{ $unit->property->address }},
                {{ $unit->property->city }},
                {{ $unit->property->state->code }},
                {{ $unit->property->zip }}
            </a>
        </div>
    @endif
</div>
