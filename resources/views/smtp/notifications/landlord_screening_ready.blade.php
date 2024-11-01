@component('mail::message')
{{-- Greeting --}}
<p>Dear {{ $landlord->name }},</p>
{{-- Intro Lines --}}

{{-- Outro Lines --}}
<p>{!! $description !!}</p>
<p><a href="{!! $url !!}">Click here to view details.</a></p>

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

{{-- Subcopy --}}
@slot('subcopy')

@endslot
@endcomponent
