@component('mail::message')
{{-- Greeting --}}
<p>Dear {{ $landlord->name }},</p>
{{-- Intro Lines --}}

{{-- Outro Lines --}}
<p>Your tenant, {{ $tenant->fullName() }} with the following contact information: {{ $tenant->email }} {{ $tenantPhone }}, {{ $property->address }}, {{ $unit->name }} has just submitted a new maintenance request.</p>
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
