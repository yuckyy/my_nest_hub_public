@component('mail::message')
{{-- Greeting --}}
{{ __('emails.dear', ['name' => $landlord]) }}
{{-- Intro Lines --}}

{{-- Outro Lines --}}
{{ "Our system indicates that the following tenant(s)  didn’t submit full payments for rent according to your lease agreement(s). Our system automatically sent those users friendly reminder emails." }}<br>

{!! "$tenants_info" !!}

<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="unsubscribeBlock"><a href="{{ route('profile/email-preferences') }}">Email Preferences</a> <a href="{{ route('unsubscribe', ['unsubscribe_token' => $user->preferences->unsubscribe_token]) }}">Unsubscribe from all notifications</a></div>

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

{{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
