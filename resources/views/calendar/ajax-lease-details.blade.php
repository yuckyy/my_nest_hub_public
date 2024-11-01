<div class="h4 pb-1"><strong id="dataDescription">Lease payment</strong></div>
<p>
    {{ $lease->unit->property->full_address }},
    {{ $lease->firstname }} {{ $lease->lastname }}
</p>
<div class="row no-gutters justify-content-between border-top border-bottom pt-3 pb-3">
    <div class="col-sm-5">
        Due Date: <strong class="float-right" id="dataDueDate">
            {{ \Carbon\Carbon::parse($event_start)->format("m/d/Y") }}
        </strong>
    </div>
    <div class="col-sm-5">
        Total Amount: <strong class="float-right" id="dataBillAmount">${{ $lease->total_by_tenant }}</strong>
    </div>
</div>

<div class="d-none">
    <div id="additionalButtons">
        <a href="{{ route('properties/units/leases', ['unit' => $lease->unit_id, 'lease_id' => $lease->id  ]) }}" class="btn btn-sm btn-primary"><i class="fal fa-file-signature mr-2"></i> View Lease</a>
    </div>
</div>

