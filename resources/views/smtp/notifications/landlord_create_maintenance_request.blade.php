@component('mail::message')
{{-- Greeting --}}
<p>Dear {{ $tenant->name }},</p>
{{-- Intro Lines --}}

{{-- Outro Lines --}}
<p>Your landlord, {{ $landlord->fullName() }} with the following contact information: {{ $landlord->email }} {{ $landlord->phone }} has submitted a new maintenance request for {{ $property->address }}, {{ $unit->name }}.</p>
<p>Details of the maintenance request:</p>
<p><strong>{{ $title }}</strong><br />{{ $description }}</p>
<p><a href='{{ $url }}'>Click here to view this request.</a></p>

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

{{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
