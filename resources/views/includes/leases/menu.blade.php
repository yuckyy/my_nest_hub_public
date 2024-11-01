<div class="nav flex-column navTabsLeft" aria-orientation="vertical">
    <a
        class="nav-link{{ request()->routeIs('properties/units/edit') ? ' active' : ''}}"
        href="{{ route('properties/units/edit', ['id' => $unit->id]) }}"
    >
        Basic
    </a>

    <a
        class="nav-link{{ request()->routeIs('properties/units/applications') ? ' active' : ''}}"
        href="{{ route('properties/units/applications', ['unit' => $unit->id]) }}"
    >
        Applications
    </a>

    <a
        class="nav-link{{ request()->routeIs('properties/units/leases') ? ' active' : ''}}"
        href="{{ route('properties/units/leases', ['unit' => $unit->id]) }}"
    >
        Lease
    </a>

    <a class="nav-link" href="{{ route('properties/edit-property/edit-unit-tenants') }}">Tenants</a>
    <a class="nav-link" href="{{ route('properties/edit-property/edit-unit-payments') }}">Payments</a>
    <a class="nav-link" href="{{ route('properties/edit-property/edit-unit-media') }}">Media</a>
    <a class="nav-link" href="{{ route('properties/edit-property/edit-unit-share') }}">Share</a>
</div>
