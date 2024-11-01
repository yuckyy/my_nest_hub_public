@component('mail::message')
    {{-- Greeting --}}
    {{ __('emails.dear', ['name' => is_string($tenant) ? $tenant : $tenant->name]) }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "Please "}}<a
        href="{{ $loginUrl }}">login</a>{{ " to MYNESTHUB and submit your payment at your earliest convenience." }}<br>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <div class="unsubscribeBlock"><a href="{{ route('profile/email-preferences') }}">Email Preferences</a> <a
            href="{{ route('unsubscribe', ['unsubscribe_token' => $user->preferences->unsubscribe_token]) }}">Unsubscribe
            from all notifications</a></div>

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
