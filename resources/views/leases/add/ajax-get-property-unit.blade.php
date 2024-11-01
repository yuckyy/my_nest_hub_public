<option hidden value="">Select Unit</option>
@foreach ($units as $unit)
    @if($unit->status != 0)
        <option value="{{ $unit->id}}">
            {{ $unit->name }}
        </option>
    @endif
@endforeach
@foreach ($units as $unit)
    @if($unit->status == 0)
        <option value="{{ $unit->id}}" disabled>
            {{ $unit->name }} (occupied)
        </option>
    @endif
@endforeach
