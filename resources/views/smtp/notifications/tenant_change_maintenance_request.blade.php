@component('mail::message')
{{-- Greeting --}}
<p>Dear {{ $landlord->name }},</p>
{{-- Intro Lines --}}

{{-- Outro Lines --}}
<p>Your tenant, {{ $tenant->fullName() }} changed the status of maintenance request <strong>{{ $title }}</strong> to <strong>{{ $status->name }}</strong>.</p>
<p>You can always view the status of this request going into your portal and clicking on the Maintenance Link from the left hand side menu
    or by <a href='{{ $url }}'>clicking here</a>.</p>

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

{{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
