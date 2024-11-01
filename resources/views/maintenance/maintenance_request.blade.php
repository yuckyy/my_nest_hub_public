<div data-ticketid="{{ $maintenanceRequest->id }}" class="card maintenanceCard callout-{{ $maintenanceRequest->priority->name === 'Critical' ? 'danger' : ($maintenanceRequest->priority->name === 'High' ? 'warning' : 'info') }}">
    <div class="pt-3 pl-3 pr-3 pb-2">
        <div class="maintenanceCardTitle">
            <div class="maintenanceCardImgCell">
                <div class="maintenanceCardImg text-white" title="Plumbing (Critical)">
                    <i class="fal fa-tools"></i>
                </div>
            </div>

            <div class="maintenanceCardTitleCell">
                <div class="ml-3 card-text">
                    <div class="text-secondary">
                        #{{ $maintenanceRequest->id }},
                        {{ Carbon\Carbon::parse($maintenanceRequest->created_at)->format("m/d/Y") }}
                    </div>
                    <div class="maintenanceTicketTitle">{{ $maintenanceRequest->name }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body pb-2 pl-3 pr-3 pt-0">
        <div class="mb-2"><span class="mr-2">{{ $maintenanceRequest->unit->name }},</span> <span class="text-secondary d-none d-lg-inline">{{ $maintenanceRequest->unit->property->full_address }}</span></div>
        <div class="maintenanceCardNav">
            @if (Auth::user()->isLandlord() || Auth::user()->isPropManager())
                <span data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="{{ $maintenanceRequest->id }}" data-record-title="{{ '#' . $maintenanceRequest->id . " - " . $maintenanceRequest->name }}" >
                    <button title="Delete Task" class="btn btn-sm btn-light mr-1 text-danger"><i class="fal fa-trash-alt"></i> Delete</button>
                </span>
                <span data-toggle="modal" data-target="#confirmArchiveModal" data-record-id="{{ $maintenanceRequest->id }}" data-record-title="{{ '#' . $maintenanceRequest->id . " - " . $maintenanceRequest->name }}" >
                    <button title="Archive Ticket" class="btn btn-sm btn-light mr-1 text-black"><i class="fal fa-file-archive"></i> Archive</button>
                </span>
                <span data-toggle="modal" class="{{ request()->routeIs('properties/units/maintenance') ? ' d-none' : ''}}" data-target="#addExpenseModal" data-record-id="{{ $maintenanceRequest->id }}" data-record-title="{{ '#' . $maintenanceRequest->id . " - " . $maintenanceRequest->name }}" >
                    <button title="Archive Ticket" class="btn btn-sm btn-light mr-1 text-black"><i class="fal fa-dollar-sign"></i> Add Expense</button>
                </span>
            @endif
            <span class="vacant-check-click"data-toggle="modal" data-category="{{ $maintenanceRequest->expense_type }}" data-target="#detailsModal"  data-property-address="{{ $maintenanceRequest->property_address }}" data-profirstname="{{$maintenanceRequest->service_pro_company_name }}{{$maintenanceRequest->service_pro_first_name }} {{$maintenanceRequest->service_pro_last_name }} {{$maintenanceRequest->service_pro_middle_name }}" data-vacant="{{ $maintenanceRequest->deleted_at }}"  data-record-id="{{ $maintenanceRequest->id }}" data-color="{{ $maintenanceRequest->color() }}" data-record-title="{{ '#' . $maintenanceRequest->id . ", " . date('m/d/Y', strtotime($maintenanceRequest->created_at)) }}" >
                <button title="View Details"  class="btn btn-sm btn-light mr-1 text-black"><i class="fal fa-eye"></i> View Details</button>
            </span>
        </div>
    </div>
</div>
