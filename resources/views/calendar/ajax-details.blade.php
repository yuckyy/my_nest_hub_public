<div class="h4 pb-1"><strong id="dataDescription">{{ $event->title }}</strong></div>
<div class="row no-gutters justify-content-between border-top border-bottom pt-3 pb-3">
    <div class="col-sm-5">
        Start Date: <strong class="float-right" id="dataDueDate">
            {{ \Carbon\Carbon::Parse($event->start)->format("m/d/Y") }}
        </strong>
    </div>
    <div class="col-sm-5">
        End Date: <strong class="float-right" id="dataDueDate">
            {{ \Carbon\Carbon::Parse($event->end)->format("m/d/Y") }}
        </strong>
    </div>
</div>
@if (!empty($event->description))
    <div class="row no-gutters justify-content-between border-bottom pb-3 pt-3">
        <div class="col-sm-2">
            Description:
        </div>
        <div class="col-sm-10">
            <span>{{ $event->description }}</span>
        </div>
    </div>
@endif
<div class="d-none">
    <div id="additionalButtons">
        <button class="btn btn-sm btn-danger" id="deleteEvent" data-eventid="{{ $event->id }}"><i class="fal fa-trash-alt mr-2"></i> Delete Event</button>
    </div>
</div>
