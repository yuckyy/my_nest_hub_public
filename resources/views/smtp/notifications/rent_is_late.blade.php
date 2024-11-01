@component('mail::message')
    {{-- Greeting --}}
    {{ __('emails.dear', ['name' => is_string($tenant) ? $tenant : $tenant->name]) }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "Rent is late for $property->full_address $unit->name, and a late fee has been added to your account." }}<br>
    {{ "Please " }}<a
        href="{{ $loginUrl }}">login</a>{{ " to MYNESTHUB and submit your payment at your earliest convenience." }}<br>


    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
