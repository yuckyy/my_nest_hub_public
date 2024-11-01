@component('mail::message')
<p>Dear {{ $firstName }} {{ $lastName }},</p>
<p>Your calendar event today</p>
<p>Start: {{ $event->start  }}</p>
<p>End: {{ $event->end  }}</p>
<p>Title: {{ $event->title  }}</p>
<p>Description: {{ $event->description  }}</p>
<p><a href="{{ $link }}">View details</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="unsubscribeBlock"><a href="{{ route('profile/email-preferences') }}">Email Preferences</a> <a href="{{ route('unsubscribe', ['unsubscribe_token' => $user->preferences->unsubscribe_token]) }}">Unsubscribe from all notifications</a></div>
@endcomponent
