<div class="profileNavTabsLeftContainer col-md-3">
    <div class="nav flex-column navTabsLeft" aria-orientation="vertical">
        <a class="nav-link{{ $active == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}">Edit Profile</a>
        <a class="nav-link{{ $active == 'password' ? ' active' : '' }}" href="{{ route('profile/password') }}">Change Password</a>
        @if (!Auth::user()->isAdmin())
{{--            <a class="nav-link{{ $active == 'photo' ? ' active' : '' }}" href="{{ route('profile/photo') }}">Update Photo</a>--}}
            @if (!session('adminLoginAsUser'))
                <a class="nav-link{{ $active == 'finance' ? ' active' : '' }}" href="{{ route('profile/finance') }}">Financial Account</a>
            @endif
            @if (!Auth::user()->isTenant())
                {{--}}
                <a class="nav-link{{ $active == 'identity' ? ' active' : '' }}" href="{{ route('profile/identity') }}">User Verification</a>
                {{--}}
                <a class="nav-link{{ $active == 'membership' ? ' active' : '' }}" href="{{ route('profile/membership') }}">My Membership</a>
            @endif
            <a class="nav-link{{ $active == 'email-preferences' ? ' active' : '' }}" href="{{ route('profile/email-preferences') }}">Email Preferences</a>
        @endif
    </div>
</div>
