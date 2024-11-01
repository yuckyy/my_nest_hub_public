@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="#">Calendar</a>
    </div>
    <div class="container-fluid">

        <div class="container-fluid">
            <div class="pt-3 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2">Calendar</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            {{--}}
            @if ( true )
            @else
                <div class="card border-warning propertyForm mb-4">
                    <div class="card-body text-center alert-warning">
                        <p class="m-0">You didn't create any properties yet. Press "Add New Property" to create new property.</p>
                    </div>
                    <div class="card-footer border-warning text-muted text-center">
                        <a href="{{ route('properties/add') }}" class="btn btn-primary btn-sm">
                            <i class="fal fa-plus-circle mr-1"></i> Add New Property
                        </a>
                    </div>
                </div>
            @endif
            {{--}}
            <div class="calendarContainer pb-4">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <!-- DETAILS MODAL-->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="loading">&nbsp;</div>
                    <div class="modal-body-load">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-2"></i>Close</button>
                    <div id="additionalButtonsPlace"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD EVENT MODAL-->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalTitle">Add Event <span id="modalEventDates"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="addEventForm" id="addEventForm" class="needs-validation" novalidate>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="eventStart" id="eventStart" value="">
                    <input type="hidden" name="eventEnd" id="eventEnd" value="">
                    <div class="modal-body bg-light">
                        <div class="mb-2">
                            <label for="eventName">Event Name <i class="required fal fa-asterisk"></i></label>
                            <input type="text" class="form-control" name="eventName" id="eventName" maxlength="190" required>
                            <span class="invalid-feedback" role="alert">Event Name is required</span>
                        </div>
                        <div class="mb-2">
                            <label for="eventDescription">Event Description</label>
                            <textarea type="text" class="form-control" name="eventDescription" id="eventDescription" maxlength="1000"></textarea>
                            <span class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button id="addEventSubmit" type="button" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Add Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ url('/') }}/vendor/moment.min.js'></script>
    <link rel="stylesheet" href="{{ url('/') }}/vendor/fullcalendar/main.min.css" />
    <script src='{{ url('/') }}/vendor/fullcalendar/main.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <script>
        var invoice_id = 0;
        var event_id = 0;
        var lease_id = 0;
        var event_start = 0;
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'title',
                    //right: 'dayGridMonth,timeGridWeek,timeGridDay today prev,next'
                    right: 'today prev,next'
                },
                editable: true,
                navLinks: false,
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap',
                businessHours: true,
                dayMaxEvents: true,
                events: {
                    url: '{{ route('fullcalendar-events') }}',
                    failure: function() {
                        //document.getElementById('script-warning').style.display = 'block'
                    }
                },
                loading: function(bool) {
                    //document.getElementById('loading').style.display =
                    //    bool ? 'block' : 'none';
                },
                selectable: true,
                selectHelper: true,
                select: function (selectionInfo) {
                    var endObj = new Date(selectionInfo.endStr);
                    if(selectionInfo.allDay){
                        endObj.setDate(endObj.getDate() - 1);
                    }
                    var start = selectionInfo.startStr;
                    var end = endObj.toISOString().slice(0, 10);

                    $('#modalEventDates').html(start + (start == end ? "" : (" - " + end)));
                    $('#eventStart').val(start);
                    $('#eventEnd').val(end);
                    $('#eventName').val('');
                    $('#eventDescription').val('');
                    $('#addEventModal').modal('show');
                },
                eventDrop: function (eventDropInfo) {
                    var start = eventDropInfo.event.startStr;
                    var end = eventDropInfo.event.endStr;
                    var eventid = eventDropInfo.event.id;
                    $.ajax({
                        url: "{{ route('fullcalendar-post') }}",
                        data: {
                            start: start,
                            end: end,
                            id: eventid,
                            type: 'drop'
                        },
                        type: "POST",
                        success: function (response) {
                            displayMessage("Event Updated Successfully");
                        }
                    });
                },
                eventClick: function (eventClickInfo) {
                    if(eventClickInfo.event.extendedProps.invoice_id) {
                        invoice_id = eventClickInfo.event.extendedProps.invoice_id;
                        event_id = 0;
                        lease_id = eventClickInfo.event.extendedProps.lease_id;
                    } else if(eventClickInfo.event.extendedProps.lease_id) {
                        lease_id = eventClickInfo.event.extendedProps.lease_id;
                        event_id = 0;
                        invoice_id = 0;
                    } else {
                        event_id = eventClickInfo.event.id;
                        invoice_id = 0;
                        lease_id = 0;
                    }
                    event_start = eventClickInfo.event.startStr;
                    $('#detailsModal').modal('show');
                    $('.fc-popover.popover').remove();
                }
            });
            calendar.render();

            $('#detailsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('.loading').show();
                modal.find('.modal-body-load').html('');
                modal.find('#additionalButtonsPlace').html('');
                $.post("{{ route('fullcalendar-details') }}", {
                    event_id: event_id,
                    invoice_id: invoice_id,
                    lease_id: lease_id,
                    event_start: event_start,
                    "_token": '{{ csrf_token() }}'
                }, function(data){
                    modal.find('.modal-body-load').html(data);
                    if($('#additionalButtons').length > 0){
                        modal.find('#additionalButtonsPlace').append($('#additionalButtons'));
                    }
                    modal.find('.loading').hide();
                });
            });

            $('#detailsModal').on('click', '#deleteEvent', function(e) {
                var ev1_id = $(this).data('eventid');
                var ev1 = calendar.getEventById(ev1_id);
                //var deleteMsg = confirm("Do you really want to delete?");
                //if (deleteMsg) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('fullcalendar-post') }}",
                    data: {
                        id: ev1.id,
                        type: 'delete'
                    },
                    success: function (response) {
                        $('#detailsModal').modal('hide');
                        ev1.remove();
                    }
                });
                //}
            });

            $('#addEventSubmit').on('click', function(e) {
                var title = $('#eventName').val();
                var description = $('#eventDescription').val();
                var start = $('#eventStart').val();
                var end = $('#eventEnd').val();

                if (title) {
                    $('#eventName').removeClass('invalid').next().hide();
                    $.ajax({
                        url: "{{ route('fullcalendar-post') }}",
                        data: {
                            title: title,
                            description: description,
                            start: start,
                            end: end,
                            type: 'add'
                        },
                        type: "POST",
                        success: function (data) {
                            $('#eventName').val('');
                            $('#eventDescription').val('');
                            $('#addEventModal').modal('hide');
                            displayMessage("Event Created Successfully");
                            calendar.addEvent({
                                id: data.id,
                                title: title,
                                start: start,
                                end: end,
                                color: '#d9534f',
                                allDay: true
                            });
                            calendar.unselect();
                        }
                    });
                } else {
                    $('#eventName').addClass('invalid').next().show();
                }

            });
        });

        function displayMessage(message) {
            toastr.success(message, 'Event');
        }
    </script>
@endsection
