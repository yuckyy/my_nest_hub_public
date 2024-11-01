<a href="{{ route('dashboard') }}" class="{{ Request::segment(1) === 'dashboard' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Dashboard <i class="fal fa-tachometer-alt"></i>
</a>
<a href="{{ route('profile') }}" class="{{ (Request::segment(1) === 'profile') && !Request::is('profile/finance') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    My Account <i class="fal fa-user"></i>
</a>
<a href="{{ route('properties') }}" class="{{ Request::segment(1) === 'properties' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Properties <i class="fal fa-home"></i>
</a>
<a href="{{ route('applications') }}" class="{{ Request::segment(1) === 'applications' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Applications {!! applicationsNavCounter() !!} <i class="fal fa-file-signature"></i>
</a>
<a href="{{ route('maintenance') }}" class="{{ Request::segment(1) === 'maintenance' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Maintenance {!! maintenanceNavCounter() !!} <i class="fal fa-tools"></i>
</a>
<a href="{{ route('reports') }}" class="{{ Request::segment(1) === 'payments' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Reports <i class="fal fa-file-invoice-dollar"></i>
</a>
<!--<a href="{{ route('expenses') }}" class="{{ Request::segment(1) === 'expenses' ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Expenses & Profit <i class="fal fa-chart-pie"></i>
</a>--!>

