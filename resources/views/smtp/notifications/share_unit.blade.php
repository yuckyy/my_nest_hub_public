@component('mail::message')
    {{-- Greeting --}}
    {{ __('emails.hi', ['name' => is_string($tenant) ? $tenant : $tenant->name]) }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "$landlord->name $landlord->lastname would like to share information about $property->full_address $unit->name." }}
    <br>
    {{ "Click " }}<a href='{{ $unit->public_link }}'>here</a> {{ " to view this property." }}
    @if (is_string($tenant))
        {{ "If you would like to join MYNESTHUB, click "}} <a
            href="{{ route('register') }}">here</a> {{" for free and quick registration." }}<br>
    @endif

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
