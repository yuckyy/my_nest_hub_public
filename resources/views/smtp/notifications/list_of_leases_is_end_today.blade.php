@component('mail::message')
{{-- Greeting --}}
{{ __('emails.dear', ['name' => $landlord]) }}
{{-- Intro Lines --}}

{{-- Outro Lines --}}
{{ "Our system indicates that lease for following tenant(s) ended today." }}<br>

{!! "$tenants_info" !!}

{{ "Please "}} <a href="{{ $login_url }}">login</a> {{" to our system, then go to specified unit and click on Lease tab. You will have an ability to End Lease by pressing End Lease button or you will see a link to change Lease End Date." }}<br>

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
