@component('mail::message')
{{-- Greeting --}}
{{ __('emails.dear', ['name' => $landlord]) }}
{{-- Intro Lines --}}

<p>Our system indicates that the following tenant(s) didn’t send full payments according to your lease agreement.</p>

{!! "$tenants_info" !!}

<p>We sent email(s) to above tenants to remind them that late fees will be applied to their account.</p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="unsubscribeBlock"><a href="{{ route('profile/email-preferences') }}">Email Preferences</a> <a href="{{ route('unsubscribe', ['unsubscribe_token' => $user->preferences->unsubscribe_token]) }}">Unsubscribe from all notifications</a></div>

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

@endcomponent
