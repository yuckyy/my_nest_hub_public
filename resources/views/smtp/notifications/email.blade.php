@component('mail::message')
    {{-- Greeting --}}
    <p>Dear {{ $name }}, </p>

    <p>We would like to thank you for registering your account on MYNESTHUB property management system.<br/><br/>
        Please <a href="{{ $verifyurl }}"
                  style="text-decoration: none; display: inline-block; background: #00b700; color:#fff; padding: 5px 10px; border-radius: 4px">Click
            here</a> to activate your account.<br/><br/>
        Or copy and paste the following URL into your web browser: <span style="color:#AAA">{{ $verifyurl }}</span>
    </p>

    {{-- Salutation --}}
    {{--}}
    @if (! empty($salutation))
    {{ $salutation }}
    @else
    @lang('Regards'),<br>
    {{ config('app.name') }}
    @endif
    {{--}}

    {{-- Subcopy --}}
    {{--}}
    @isset($actionText)
    @slot('subcopy')
    @lang(
        "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
        'into your web browser: [:actionURL](:actionURL)',
        [
            'actionText' => $actionText,
            'actionURL' => $verifyurl,
        ]
    )
    @endslot
    @endisset
    {{--}}
@endcomponent
