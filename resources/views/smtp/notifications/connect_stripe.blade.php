@component('mail::message')
{{-- Greeting --}}
{{ __('emails.dear', ['name' => $landlord]) }}

{{-- Intro Lines --}}
In order to get payments from your tenants, you have to click link below and follow instructions.

{{-- Action Button --}}
@isset($actionText)
@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
Once this is complete, you will be able to get payments.

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

{{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
