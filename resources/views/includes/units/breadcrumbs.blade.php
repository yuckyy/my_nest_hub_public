<div class="container-fluid d-none d-md-block breadCrumbs">
    <a href="{{ route('properties') }}">Properties</a>
    >
    @if (!empty($unit->property))
    <a href="{{ route('properties/edit', ['id' => $unit->property->id]) }}">{{ $unit->property->address }}</a>

    >
    <a href="#">{{ $unit->name }}</a>
    @endif
</div>
