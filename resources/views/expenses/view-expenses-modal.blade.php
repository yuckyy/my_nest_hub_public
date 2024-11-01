<div class="h4 pb-1"><strong id="dataDescription">{{ $expense->name }}</strong></div>
<div class="row no-gutters justify-content-between border-top border-bottom pt-3 pb-3">
    <div class="col-sm-5">
        Date: <strong class="float-right" id="dataDueDate">
            {{ \Carbon\Carbon::parse($expense->expense_date)->format("m/d/Y") }}
        </strong>
    </div>
    <div class="col-sm-5">
        Bill Amount: <strong class="float-right" id="dataBillAmount">${{ $expense->amount }}</strong>
    </div>
</div>
@if(!empty($expense->notes))
    <div class="pt-3 pb-3 border-bottom">
        Notes: <strong class="ml-4" id="dataBillNotes">{{ $expense->notes }}</strong>
    </div>
@endif
@if(!empty($expense->file))
    <div class="pt-3">
        <a class="btn btn-sm btn-primary mr-1" target="_blank" href="{{ $expense->fileUrl() }}" data-toggle="tooltip" data-placement="top" title="View Uploaded Document"><i class="fal fa-file-alt mr-2"></i> View Uploaded Document</a>
    </div>
@endif

