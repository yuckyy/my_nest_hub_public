@component('mail::message')
    {{-- Greeting --}}
    {{ "Hi, $landlordEmail, welcome to MYNESTHUB!" }}
    {{-- Intro Lines --}}

    {{-- Outro Lines --}}
    {{ "Hello, there was a " . $sharedUser->roles->first()->name  . " application submitted through MYNESTHUB.com. In order to view this application, you need to register with us. MYNESTHUB is a FREE software and there are tons of benefits using us:" }}
    <ul>
        <li>Collect rent and payments</li>
        <li>Manage leases</li>
        <li>Obtain rental applications</li>
        <li>View your rental accounting and reports</li>
        <li>Organize maintenance request</li>
        <li>and much more. Click <a
                href="{{ route('register', ['redirect_link' => route('applications/view', ['id' => $application->id])]) }}">here</a>
            for your free and quick registration.
        </li>
    </ul>

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
    @endif

    {{-- Subcopy --}}
    @slot('subcopy')

    @endslot
@endcomponent
