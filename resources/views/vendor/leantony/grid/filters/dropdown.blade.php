<select name="{{ $name }}" id="{{ $id }}" form="{{ $formId }}" class="{{ $class }}" onchange="this.form.submit()">
    <option value="">Select</option>
    @foreach($data as $k => $v)
        @if(request($name) !== null && request($name) == $k)
            <option value="{{ $k }}" selected>{{ $v }}</option>
        @else
            <option value="{{ $k }}">{{ $v }}</option>
        @endif
    @endforeach
</select>
