@component('mail::message')
    {{-- Greeting --}}
    {{ "Hi, $landlord->name, welcome to MYNESTHUB!" }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "You received an application. To view this application click " }} <a
        href="{{ route('applications/view', ['id' => $application->id]) }}">here</a>. {{"By pressing this link, system"}}
    {{ "must perform user authentication and after authentication it needs to take user to View that" }}<br>
    {{ "application page." }}<br><br>
    {{ "Thanks for visiting " }} <a href='{{ route('properties') }}'>MYNESTHUB</a>! <br>

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
