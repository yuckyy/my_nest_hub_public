<div class="nav flex-column navTabsLeft" aria-orientation="vertical">
    <a class="nav-link{{ request()->routeIs('properties/units/edit') ? ' active' : ''}}"
       href="{{ route('properties/units/edit', ['id' => $unit ?? ''->id]) }}">Basic</a>

    <a class="nav-link{{ request()->routeIs('properties/units/applications') ? ' active' : ''}}{{ request()->routeIs('properties/add-from-list') ? ' active2' : ''}}"
       href="{{ route('properties/units/applications', ['unit' => $unit ?? ''->id]) }}">Applications @if(isset($applicationsCount))({{$applicationsCount}})@endif</a>

    <a class="nav-link{{ request()->routeIs('properties/units/leases') ? ' active' : ''}}"
       href="{{ route('properties/units/leases', ['unit' => $unit ?? ''->id]) }}">Lease</a>

    <a class="nav-link{{ request()->routeIs('properties/units/maintenance') ? ' active' : ''}}{{ request()->routeIs('properties/units/maintenance') ? ' active2' : ''}}"
       href="{{ route('properties/units/maintenance', ['unit' => $unit ?? ''->id]) }}">Maintenance</a>

{{--    <!--<a class="nav-link{{ request()->routeIs('properties/units/tenants') ? ' active' : ''}}"--}}
{{--       href="{{ route('properties/units/tenants', ['unit' => $unit ?? ''->id]) }}">Tenants</a>--!>--}}

    <a class="nav-link{{ request()->routeIs('properties/units/payments') ? ' active' : ''}}"
       href="{{ route('properties/units/payments', ['unit' => $unit ?? ''->id]) }}">Payments</a>

    <a class="nav-link {{ request()->routeIs('properties/units/expenses') ? 'active' : '' }}"
       href="{{ route('properties/units/expenses', ['unit' => $unit ?? ''->id]) }}">Expenses</a>

    <a class="nav-link{{ request()->routeIs('properties/units/media') ? ' active' : ''}}"
       href="{{ route('properties/units/media', ['unit' => $unit ?? ''->id]) }}">Media</a>

    <a class="nav-link {{ request()->routeIs('properties/units/share') ? 'active' : '' }}"
       href="{{ route('properties/units/share', ['unit' => $unit ?? ''->id]) }}">Marketing</a>
</div>
