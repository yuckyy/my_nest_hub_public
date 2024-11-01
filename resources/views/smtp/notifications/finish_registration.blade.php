@component('mail::message')
    {{-- Greeting --}}
    {{ __('emails.hi', ['name' => $tenant->name]) }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "You received an invitation from $landlord->name $landlord->lastname to join MYNESTHUB." }}<br>
    {{ "MYNESTHUB.com is a free online system, which helps landlords and property managers to manage and organize properties." }}
    <br>
    {{ "Our system was designed to help you to save time and money" }} <a href='{{ $url }}'>Click here</a>
    {{ " to join invitation. Thank you for using our application!" }}<br>

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
