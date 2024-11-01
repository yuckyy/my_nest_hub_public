<div class="text-center text-sm-left">
    <h1 class="h2 d-inline-block">
        <span class="text-secondary">Landlord : {{ $lease->unit->property->user->name }}</span><br>
        <span class="text-secondary">Landlord email : {{ $lease->unit->property->user->email }}</span>
    </h1>

    @if ($lease->unit->isOccupied())
    <span class="badge badge-danger align-top">Occupied</span>
    @else
    <span class="badge badge-success align-top">Vacant</span>
    @endif

    <h6 class="text-center text-sm-left pb-3">
        {{ $lease->unit->property->address }},
        {{ $lease->unit->property->city }},
        {{ $lease->unit->property->state->code }},
        {{ $lease->unit->property->zip }}
    </h6>
</div>
