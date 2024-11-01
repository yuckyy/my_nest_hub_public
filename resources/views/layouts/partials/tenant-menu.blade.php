<a href="{{ route('dashboard') }}" class="{{ Request::segment(1) === 'dashboard' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Dashboard <i class="fal fa-tachometer-alt"></i>
</a>
<a href="{{ route('profile') }}" class="{{ (Request::segment(1) === 'profile') && !Request::is('profile/finance') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    My Account <i class="fal fa-user"></i>
</a>
<a href="{{ route('applications') }}" class="{{ Request::segment(1) === 'applications' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Applications <i class="fal fa-file-signature"></i>
</a>
<a href="{{ route('tenant/leases') }}" class="{{ Request::is('leases') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Lease <i class="fal fa-newspaper"></i>
</a>
<a href="{{ route('payments') }}" class="{{ Request::segment(1) === 'payments' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Payments <i class="fal fa-dollar-sign"></i>
</a>
<a href="{{ route('maintenance') }}" class="{{ Request::segment(1) === 'maintenance' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Maintenance <span id="maintenanceRequestsCountInMenu" class="badge badge-dark">{{ Auth::user()->getNewMaintenanceRequestsCount() }}</span> <i class="fal fa-tools"></i>
</a>
{{--
@if (!session('adminLoginAsUser'))
<a href="{{ route('profile/finance') }}" class="{{ Request::is('profile/finance') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Financial Account <i class="fal fa-credit-card"></i>
</a>
@endif
--}}