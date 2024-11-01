@component('mail::message')
{{-- Greeting --}}
<p>Hello,</p>
{{-- Intro Lines --}}

{{-- Outro Lines --}}
<p>
landlord {{ $landlord->name }} {{ $landlord->lastname }} would like you to submit a rental application
@if(!empty($unit))
for {{ $unit->property->fullAddress }}, {{ $unit->name }}
@endif
</p>
@if(!empty($unit))
<p>Please click <a href="{{ env('APP_URL').'application-register-apply?email=' . $toEmail . '&landlord_id=' . $landlord->id . '&unit_id=' . $unit->id . '&role=tenant'}}">here</a> to continue.</p>
@else
<p>Please click <a href="{{ env('APP_URL').'application-register-apply?email=' . $toEmail . '&landlord_id=' . $landlord->id . '&role=tenant'}}">here</a> to continue.</p>
@endif

{{-- Salutation --}}
@if (! empty($salutation))
    {{ $salutation }}
@else
@endif

@endcomponent
