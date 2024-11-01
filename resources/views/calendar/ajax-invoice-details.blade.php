<div class="h4 pb-1"><strong id="dataDescription">{{ $invoice->description }}</strong></div>
<p>
    {{ $invoice->lease->unit->property->full_address }},
    {{ $invoice->lease->firstname }} {{ $invoice->lease->lastname }}
</p>
<div class="row no-gutters justify-content-between border-top border-bottom pt-3 pb-3">
    <div class="col-sm-5">
        Due Date: <strong class="float-right" id="dataDueDate">
            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->due_date)->format("m/d/Y") }}
        </strong>
    </div>
    <div class="col-sm-5">
        Invoice Amount: <strong class="float-right" id="dataBillAmount">${{ $invoice->amount }}</strong>
    </div>
</div>
@foreach ($invoice->payments as $p)
    <div class="row no-gutters justify-content-between pt-3 pb-3">
        <div class="col-sm-5">
            Paid On: <strong class="float-right" id="dataPaidOn">{{ \Carbon\Carbon::parse($p->pay_date)->format("m/d/Y") }}</strong>
        </div>
        <div class="col-sm-5">
            Paid Amount: <strong class="float-right" id="dataPaidAmount">${{ $p->amount }}</strong>
        </div>
    </div>
    <div class="row no-gutters justify-content-between pt-3 pb-3 border-top border-bottom">
        <div class="col-sm-12">
            Record Added: <strong class="float-right" id="dataRecordAdded">{{ payMethodStringFilter($p->pay_method) }}</strong>
        </div>
    </div>
    @if ($p->finance_id)
        <div class="row no-gutters justify-content-between pt-3 pb-3 border-bottom">
            <div class="col-sm-12">
                Transaction ID: <strong class="float-right" id="dataRecordAdded">{{ transactionIdStringFilter($p->transaction_id) }}</strong>
            </div>
        </div>
    @endif
    @if ($p->note)
        <div class="row no-gutters justify-content-between border-bottom pb-3 pt-3">
            <div class="col-sm-1">
                Note:
            </div>
            <div class="col-sm-11">
                <span id="dataNote">{{ $p->note }}</span>
            </div>
        </div>
    @endif
@endforeach
@if ($invoice->transaction_history)
    <div class="row no-gutters justify-content-between pt-3 pb-3 border-bottom">
        <div class="col-sm-12">
            ACH Transaction History: <span class="float-right">{!! $invoice->transaction_history !!}</span>
        </div>
    </div>
@endif

<div class="row no-gutters justify-content-between pt-3">
    <div class="col-sm-5 offset-sm-7">
        Balance: <strong class="float-right {{ $invoice->balance >= 0 ? 'text-success' : 'text-danger' }}" id="dataBalance">
            {{ $invoice->balance >= 0 ? '$'.$invoice->balance : str_replace("-","-$",$invoice->balance) }}</strong>
    </div>
</div>
<div class="d-none">
    <div id="additionalButtons">
        <a href="{{ route('properties/units/payments', ['unit' => $invoice->lease->unit_id, 'lease' => $invoice->lease->id ]) }}#invoices" class="btn btn-sm btn-primary mr-2"><i class="fal fa-file-invoice-dollar mr-2"></i> View Lease Invoices</a>
        <a href="{{ route('properties/units/leases', ['unit' => $invoice->lease->unit_id, 'lease_id' => $invoice->lease->id  ]) }}" class="btn btn-sm btn-primary"><i class="fal fa-file-signature mr-2"></i> View Lease</a>
    </div>
</div>

