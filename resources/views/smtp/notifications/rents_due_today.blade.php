@component('mail::message')
    {{-- Greeting --}}
    {{ __('emails.dear', ['name' => is_string($tenant) ? $tenant : $tenant->name]) }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "Your rent payment is due today for $property->full_address $unit->name" }}.<br>
    {{ "Please login to MYNESTHUB and submit your payment at your earliest convenience." }}<br>


    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
        @lang('Regards'),
        {{ config('app.name') }}
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
