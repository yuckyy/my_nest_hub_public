<a href="{{ route('profile') }}" class="{{ Request::segment(1) === 'profile' ? 'active' : "" }} list-group-item list-group-item-action active d-flex justify-content-between align-items-center">
    Account <i class="fal fa-user"></i>
</a>
<a href="{{ route('users') }}" class="{{ Request::is('admin/users') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Users <i class="fal fa-users"></i>
</a>
<a href="{{ route('coupons') }}" class="{{ Request::is('admin/coupons') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Coupons <i class="fal fa-tags"></i>
</a>
<a href="{{ route('products') }}" class="{{ Request::is('admin/products') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Product Prices <i class="fal fa-money-bill-alt"></i>
</a>
<a href="{{ route('admin/addons') }}" class="{{ Request::is('admin/addons') ? 'active' : "" }} list-group-item list-group-item-action d-flex justify-content-between align-items-center">
    Addons <i class="fal fa-puzzle-piece"></i>
</a>
