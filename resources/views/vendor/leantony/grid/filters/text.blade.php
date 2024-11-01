<input type="text" name="{{ $name }}" id="{{ $id }}"
       form="{{ $formId }}"
       class="{{ $class }}" value="{{ request($name) }}"
       @foreach($dataAttributes as $k => $v)
       data-{{ $k }}={{ $v }}
        @endforeach
>
