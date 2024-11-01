<ul class="list-group">
    @if (count($userPlans) > 0 )
        @foreach ($userPlans as $p)
            <li class="list-group-item">{{ $p->user->full_name }}</li>
        @endforeach
    @else
        <li class="list-group-item text-danger">There are no users used this coupon.</li>
    @endif
</ul>
