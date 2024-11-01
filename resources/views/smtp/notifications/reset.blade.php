@component('mail::message')
    {{-- Greeting --}}
    {{ __('emails.hi', ['name' => $name]) }}
    {{-- Intro Lines --}}
    @foreach ($introLines as $line)

        {{ $line }}
    @endforeach
    {{-- Action Button --}}
    @isset($actionText)
            <?php
            switch ($level) {
                case 'success':
                case 'error':
                    $color = $level;
                    break;
                default:
                    $color = 'primary';
            }
            ?>
        @component('mail::button', ['url' => $actionUrl, 'color' => $color])
            {{ $actionText }}
        @endcomponent
    @endisset

    {{-- Outro Lines --}}
    @foreach ($outroLines as $line)
        {{ $line }}

    @endforeach

    {{ __('emails.outroline1', ['count' => $count])}}
    <br>
    {{ __('emails.outroline2')}}<br>
    {{ __('emails.outroline3')}} <a href="{{$url}}">MYNESTHUB</a>
    {{ __('emails.outroline4')}}<br>
    {{ __('emails.outroline5')}} <a href="{{$url}}">MYNESTHUB</a>!

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @isset($actionText)
        @slot('subcopy')
            @lang(
                "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                'into your web browser: [:actionURL](:actionURL)',
                [
                    'actionText' => $actionText,
                    'actionURL' => $actionUrl,
                ]
            )
        @endslot
    @endisset
@endcomponent
