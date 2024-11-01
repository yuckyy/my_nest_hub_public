@component('mail::message')
{{-- Greeting --}}
{{ __('emails.dear', ['name' => is_string($tenant) ? $tenant : $tenant->name]) }}
{{-- Intro Lines --}}

{{-- Outro Lines --}}
{{ "Your landlord, $landlord, has enabled online rent payments so you can begin conveniently paying your rent online with a bank account or credit card. You can also set up autopay so you never forget about rent." }}<br>

@component('mail::button', ['url' => $actionUrl, 'color' => 'blue'])
{{ "Make Payment" }}
@endcomponent

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

{{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
