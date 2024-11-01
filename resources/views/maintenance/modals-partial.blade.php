<!-- ADD EXPENSES -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalTitle">Add Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <form class="checkUnload" method="POST" action="{{ route('add-expense') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="p-3 bg-light">
                        <input type="hidden" name="unit_id" value="@if(isset($unit)){{ $unit->id }} @else @endif">
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="billType">Expense Type <i class="required fal fa-asterisk"></i></label>
                                <div id="org_div3">
                                    <div id="org_div2">
                                    </div>
                                </div>
                                <select  data-toggle="dropdown"id="org_div1" name="expense_type" data-toggle="collapse"  id="billType" data-target="#main_nav"class=" dropdown custom-select form-control @error('expense_type') is-invalid @enderror @error('expense_name') is-invalid @enderror" >
                                </select>
                                <input  id="org_div4" name="pid" class="d-none form-control " >
                                <div class="collapse navbar-collapse divdropstyle list"style="height: 200px" id="main_nav">
                                    <ul class="navbar-nav "style="background-color: #fff">
                                        <li class="nav-item " >
                                            <ul class="dropdown-menu dropdown-menu-drop uldropstyles">
                                                @foreach( $allcategory as $category)
                                                    <li><a class="dropdown-item" disabled > {{$category->name}}    ></a>
                                                        <ul class="submenu dropdown-menu ulsubdropstyle">
                                                            @foreach( $allsubcategory as $subcategory)
                                                                @if($subcategory->pid === $category->id)
                                                                    <li><a class="dropdown-item dropdown-item-click"data-pid="{{$subcategory->pid}}" data-category="{{$subcategory->id}}"  > {{$subcategory->name}}</a></li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                @error('expense_type')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                                @error('expense_name')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="expense_amount">Amount <i class="required fal fa-asterisk"></i></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                    <input type="text" class="form-control @error('expense_amount') is-invalid @enderror" name="expense_amount" id="expense_amount" data-type="currency" maxlength="10" value="{{ old('expense_amount') ?? '' }}">
                                    @error('expense_amount')
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="expense_date">Date <i class="required fal fa-asterisk"></i></label>
                                <input name="expense_date" id="expense_date" type="date" value="{{ old('expense_date') ? old('expense_date') : \Carbon\Carbon::now()->addDays(5)->format('Y-m-d') }}" class="form-control @error('expense_date') is-invalid @enderror">
                                @error('expense_date')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">

                                <label>&nbsp;</label>
                                <div class="custom-control custom-checkbox pt-2 ml-2">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input @error('monthly') is-invalid @enderror"
                                        id="monthly"
                                        name="monthly"
                                    >
                                    <label id="monthlyLabel" class="custom-control-label" for="monthly">
                                        Repeat Monthly
                                    </label>
                                    <span class="invalid-feedback" role="alert">
                                                @error('monthly')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>
                            </div>
                        </div>
                        <div id="collapseMonthly" aria-expanded="false" class="collapse">
                            <div class="form-row justify-content-end">
                                <div class="col-md-3 mb-3">
                                    <label for="endDate">End Date</label>
                                    <input
                                        type="date"
                                        value="{{ old('end_date') }}"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        id="endDate"
                                        name="end_date"
                                        disabled
                                    >
                                    <span class="invalid-feedback" role="alert">
                                                @error('end_date')
                                        {{ $message }}
                                        @enderror
                                            </span>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-checkbox pt-2 ml-2">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input @error('no_end_date') is-invalid @enderror"
                                            id="no_end_date"
                                            name="no_end_date"
                                            checked
                                        >
                                        <label class="custom-control-label" for="no_end_date">
                                            No end date
                                        </label>
                                        <span class="invalid-feedback" role="alert">
                                                    @error('no_end_date')
                                            {{ $message }}
                                            @enderror
                                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-9 mb-3">
                                <label for="notesField">Notes</label>
                                <textarea style="height: 34px" title="Notes" class="form-control" id="notesField" name="notes" maxlength="4000">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="expense_file">Document</label>
                                <div class="custom-file customSingleFile">
                                    <input type="file" class="custom-file-input" id="expense_file" name="expense_file">
                                    <label for="expense_file" class="custom-file-label" data-browse="">Upload File</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button class="btn btn-primary btn-sm"><i class="fal fa-check-circle mr-1"></i> Add Expenses</button>
                    </div>

                </form>
            </div>

            </div>
        </div>
    </div>
</div>

<!-- DELETE RECORD -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <p>You are about to delete maintenance request <b><i class="title"></i></b>, this procedure is irreversible.</p>
                <div>Do you want to proceed?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger btn-ok"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- ARCHIVE RECORD -->
<div class="modal fade" id="confirmArchiveModal" tabindex="-1" role="dialog" aria-labelledby="confirmArchiveModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmArchiveModalTitle">Confirm Archive</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <p>Are you sure you would like to archive this maintenance request <b><i class="title"></i></b>?</p>
                <div>You can always view archive records by clicking on View Archive button.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary btn-ok"><i class="fal fa-file-archive mr-1"></i> Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW DETAILS -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="modal-title" id="detailsModalTitle">Maintenance Request <span class="ticketTitle">00</span></h5>
                        </div>
                        <div class="col-5">
                            <h7 class="modal-title " >Property Address: <span class="property-address"></span></h7>
                        </div>
                        <div class="col-1">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                </div>


            </div>
            <div class="modal-body bg-light">

                <div class="row">
                    <div class="col-lg-6">
                        <div id="coloredDescription" class="alert mb-3">
                            <h5 class="alert-heading"><i class="fal fa-tools"></i>&nbsp;&nbsp; <span id="ticketNumber"> </span></h5>
                            <hr />
                            <p class="card-text" id="ticketDescription">
                            </p>
                        </div>
                        <h6 id="contactTitle" class="mt-3 mb-1">
                            Contact Person:
                        </h6>
                        <div id="contactContent" class="mb-3 pb-1 border-bottom"></div>
                        @if (Auth::user()->isLandlord() )
{{--                            @if (isset($servicePros))--}}
                                <h6 id="serviceProTitle" class="mt-3 mb-1">
                                    Assigned Service Professional:
                                </h6>
                                <div id="serviceProContent" class="mb-3 pb-1 border-bottom"></div>
                                <h6 class="messageTitle mt-3 mb-2">
                                    Messages:
                                </h6>
{{--                            @endif--}}
                        @endif
                        <div class="messageList mb-3 pt-1 pb-1 border-top border-bottom"></div>
                        @if (empty(Request::get('archived')))
                            <div class="mb-2 vacant-check-d">
                                <label for="newMessageText"><span class="messageLabelNoMessages">Send</span><span class="messageLabelHasMessages">New</span> Message<i class="required fal fa-asterisk"></i></label>

                                <span style="display: none;" id="modal-new-message-error" class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>

                                <textarea type="text" class="newMessageText form-control form-control-sm" name="message" id="newMessageText"></textarea>
                            </div>
                        @endif
                        @if (empty(Request::get('archived')))
                            <div class="text-right vacant-check-d">
                                <button id="sendMessage" type="button" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Send Message</button>
                            </div>
                        @endif

                    </div>

                    <div class="col-lg-6 mt-2 mt-lg-0" id="uploadWindow2">
                        <div class="leaseFormFileUploadBox text-center bg-white border p-2 pb-3">
                            <div class="pb-2">
                                <small>
                                    Upload here images, files or documents.<br>
                                    Maximum size: 10Mb.<br>
                                    Allowed file types: doc, pdf, txt, xls, csv, jpg, png, gif, mp4, avi, mpeg.
                                </small>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="documentUpload2" multiple data-uploadwindow="#uploadWindow2">
                                <div class="custom-file-label btn btn-sm btn-primary" for="documentUpload2" data-browse=""><i class="fal fa-upload"></i> Upload Documents</div>
                            </div>
                        </div>
                        <div class="pt-3">
                            <ul id="documentsContent" class="sharedFileList list-group">
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- NEW TICKET -->
<div class="modal fade" id="newTicketModal" tabindex="-1" role="dialog" aria-labelledby="editCardModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newTicketModalTitle">New Maintenance Ticket @if(!empty($draft)) <span class="text-muted">(Saved Draft)</span> @endif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="maintenanceForm" id="maintenanceForm" class="needs-validation" novalidate>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="maintenanceRequestId" name="maintenance_request_id" value="{{ empty($draft) ? "" : $draft->id }}">
                <script>
                    window.onload = function(){
                        document.getElementById("maintenanceRequestId").value = "{{ empty($draft) ? "" : $draft->id }}";
                    }
                </script>
                <div id="editMaintenanceFormModalForm">
                    @if (Auth::user()->isTenant() && (App\Models\Lease::where('email', Auth::user()->email)->count() == 0))
                        <div class="modal-body bg-light">
                            <div class="alert mb-3 alert-warning">
                                <p class="card-text">
                                    You can’t create maintenance request because your don’t have a lease.
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
                        </div>
                    @else
                        <div class="modal-body bg-light">

                            <div class="row">
                                <div class="col-lg-6">

                                    <div class="row pb-2">
                                        <div class="col-md-3">
                                            <label for="newTicketModalPriority">Priority <i class="required fal fa-asterisk"></i></label>
                                            <select id="newTicketModalPriority" name="priority" class="custom-select custom-select-sm" required>
                                                @foreach ($priorities as $priority)
                                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="invalid-feedback" role="alert"></span>
                                        </div>

                                        <div class="col-md-9 selectParent">
                                            <label for="newTicketModalProperty">Property/Unit <i class="required fal fa-asterisk"></i></label>
                                            <div class="selectpickerBox border rounded">
                                                <select id="newTicketModalProperty" name="property" class="selectpicker form-control form-control-sm" data-live-search="true" required>
                                                    @foreach ($units as $unit)
                                                        <option data-tokens="{{ $unit->property->address }}/{{ $unit->name }}" value="{{ $unit->id }}">{{ $unit->property->address }}, {{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span class="invalid-feedback invalid-feedback-select" role="alert"></span>
                                        </div>
                                    </div>
                                    @if (Auth::user()->isLandlord() )
                                    <div class="mb-2">
                                        <label for="newTicketModalServicePro">Assign Service Professional <i class="required fal fa-asterisk"></i></label>
                                        <select id="newTicketModalServicePro" name="service_pro" class="selectpicker form-control form-control-sm" data-live-search="true" required>
                                            @foreach ($servicePros as $servicePro)
                                                <option data-tokens="" value="{{ $servicePro->id }}">
                                                    @if($servicePro->display_as_company > 0)
                                                        {{$servicePro->company_name}} (company)
                                                    @else
                                                        {{$servicePro->first_name}} {{$servicePro->last_name}} {{$servicePro->middle_name}}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" role="alert"></span>
                                    </div>
                                    @endif
                                    <div class="mb-2">
                                        <label for="newTicketModalName">Ticket name <i class="required fal fa-asterisk"></i></label>
                                        <input type="text" class="form-control" maxlength="50" name="newTicketModalName" id="newTicketModalName" required>
                                        <span class="invalid-feedback" role="alert"></span>
                                    </div>

                                    <div class="mb-2">
                                        <label for="newTicketModalDescription">Describe Your Problem <i class="required fal fa-asterisk"></i></label>
                                        <textarea type="text" class="form-control" maxlength="500"name="maintenancDescription" id="newTicketModalDescription" required></textarea>
                                        <span class="invalid-feedback" role="alert"></span>
                                    </div>

                                </div>
                                <div class="col-lg-6 mt-2 mt-lg-0" id="uploadWindow1">
                                    <div class="leaseFormFileUploadBox text-center bg-white border p-2 pb-3">
                                        <div class="pb-2">
                                            <small>
                                                Upload here images, files or documents.<br>
                                                Maximum size: 10Mb.<br>
                                                Allowed file types: doc, pdf, txt, xls, csv, jpg, png, gif, mp4, avi, mpeg.
                                            </small>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="documentUpload" multiple data-uploadwindow="#uploadWindow1">
                                            <div class="custom-file-label btn btn-sm btn-primary" for="documentUpload" data-browse=""><i class="fal fa-upload"></i> Upload Documents</div>
                                        </div>
                                    </div>
                                    <div class="pt-3">
                                        <ul id="sharedFileList" class="sharedFileList list-group">
                                            @foreach ($draftDocuments as $document)
                                                <li class="p-2 list-group-item list-group-item-action" data-documentid="{{ $document->id }}">
                                                    <a class="sharedFileLink" href="/storage/{{ $document->filepath }}" target="_blank">{!! $document->icon() !!} <span>{{ $document->name }}</span></a> <button class="btn btn-sm btn-cancel deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt"></i></button>
                                                    <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                            <button id="newTicketModalSumbit" type="button" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Submit a ticket</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
