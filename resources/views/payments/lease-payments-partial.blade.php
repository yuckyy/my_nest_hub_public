<div class="card propertyForm mb-4">

    <div class="row no-gutters">
        <div class="col-md-4 border-right bg-light border-right">
            <div class="card-header">
                Current Balance
            </div>
            <div class="card-body totalBalanceCardBody">
                <div class="inRowComment text-center">
                    Based on in-progress and completed transactions
                </div>
                <div class="totalBalanceBox pb-0">
                    <div class="totalBalanceLabel">
                        Deposit
                    </div>
                    <div class="totalBalanceAmount totalDeposit last12Month1">
                        ${{ $lease->deposit12 }}
                    </div>
                    <div class="totalBalanceAmount totalDeposit last12Month0 d-none">
                        ${{ $lease->deposit }}
                    </div>
                </div>
                <div class="totalBalanceBox">
                    <div class="totalBalanceLabel">
                        Outstanding
                    </div>
                    <div
                        class="totalBalanceAmount {{ $lease->outstanding12 > 0 ? 'totalOutstanding' : 'text-success'}} last12Month1">
                        ${{ $lease->outstanding12 }}
                    </div>
                    <div
                        class="totalBalanceAmount {{ $lease->outstanding > 0 ? 'totalOutstanding' : 'text-success'}} last12Month0 d-none">
                        ${{ $lease->outstanding }}
                    </div>
                </div>

                <div class="text-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="last12Month1" value="1" name="last12Month" class="custom-control-input"
                               checked>
                        <label class="custom-control-label" for="last12Month1">Last 12 Month</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="last12Month0" value="0" name="last12Month" class="custom-control-input">
                        <label class="custom-control-label" for="last12Month0">View All</label>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-8 bg-light">
            <div class="card-header d-flex justify-content-between">
                Lease Snippet Information
                @if (!$user->isTenant())
                    @if (($selectedLease !== false) && empty($selectedLease->deleted_at) && empty($selectedLease->tenantLastLogin()) )
                        <div class="ml-1 mr-auto" data-toggle="tooltip" data-placement="top"
                             title="Tenant has not registered. Resend an Invitation Email." style="margin: -5px 0">
                            <button class="btn btn-light btn-sm text-muted" data-toggle="modal"
                                    data-target="#confirmResendEmailModal">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                            </button>
                        </div>
                    @endif
                @endif
                <a href="{{ !$user->isTenant() ? route('properties/units/leases', ['unit' => $unit->id, 'lease_id' => $lease->id]) : route('tenant/leases', ['lease_id' => $lease->id]) }}"
                   class="btn btn-light btn-sm text-muted" style="margin: -5px 0">View Lease <i
                        class="fal fa-eye ml-1"></i></a>
            </div>
            <div class="card-body">
                <table class="table snippetTable">
                    <tbody>
                    <tr>
                        <td>Monthly Rent:</td>
                        <td class="w-50">${{ $lease->amount }}</td>
                    </tr>
                    <tr>
                        <td>Total Lease Bills:</td>
                        <td>${{ $lease->total_collect_bills}}</td>
                    </tr>
                    <tr>
                        <td>Total Monthly Rent:</td>
                        <td><strong>${{ $lease->total_monthly }} due on {{ $lease->monthly_due_date }}st</strong></td>
                    </tr>
                    <tr>
                        <td>Total Monthly Rent Assistance:</td>
                        <td><strong>${{ $lease->total_assistance }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total Monthly Rent By Tenant Expected:</td>
                        <td><strong>${{ $lease->total_by_tenant }}</strong></td>
                    </tr>
                    @if (!$user->isTenant())
                        <tr>
                            <td>Receive Recurrent Payment:</td>
                            <td>
                                @if(Auth::user()->financialCollectRecurringAccounts()->count() == 0)
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-exclamation-triangle text-danger" id="addAccBut"
                                                   data-toggle="tooltip" data-placement="top"
                                                   title="The financial account is not connected"></i>
                                            </div>
                                        </div>
                                        <select name="financeAccount"
                                                class="form-control form-control-sm custom-select fixedMaxInputWidth"
                                                id="financeAccount"
                                                data-current="{{ $financeAccount ? $financeAccount->id : '' }}">
                                            <option value="">Not Connected</option>
                                            <option value="_new">Add Financial Account</option>
                                        </select>
                                    </div>
                                @else
                                    <select name="financeAccount"
                                            class="form-control form-control-sm custom-select fixedMaxInputWidth"
                                            id="financeAccount"
                                            data-current="{{ $financeAccount ? $financeAccount->id : '' }}">
                                        <option value="">Not Connected</option>
                                        @foreach (Auth::user()->financialCollectRecurringAccounts() as $f)
                                            <option value="{{ $f->id }}"
                                                    @if(!empty($financeAccount) && ($financeAccount->id == $f->id)) selected @endif>{{ $f->nickname }}</option>
                                        @endforeach
                                        <option value="_new">Add Financial Account</option>
                                    </select>
                                @endif
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            @if ($user->isTenant())
                @if ($lease->trashed())
                    <div class="text-danger text-small pl-4">Your lease ended. You can't make any payments. Contact your
                        landlord directly to arrange new payment.
                    </div>
                @endif
                @if ($lease->tenantLinkedFinance())
                    <form action="{{ route('recurring-stop') }}" method="post" class="recurring-stop">
                        @csrf
                        <input type="hidden" name="finance_id" value="{{ $lease->tenantLinkedFinance()->id }}">
                        <div class="card-footer d-lg-flex justify-content-between align-items-center">
                            <div class="mb-2 mb-lg-0 pl-2">
                                <strong>Your Credit Card will be automatically charged by
                                    {{ \Carbon\Carbon::now()->format('d') < $lease->tenantLinkedFinance()->recurring_payment_day
                                        ? \Carbon\Carbon::now()->firstOfMonth()->addDays($lease->tenantLinkedFinance()->recurring_payment_day-1)->format('M d, Y')
                                        : \Carbon\Carbon::now()->addMonth()->firstOfMonth()->addDays($lease->tenantLinkedFinance()->recurring_payment_day-1)->format('M d, Y')  }}
                                </strong>
                            </div>
                            <div class="w-50 pl-2">
                                <span data-toggle="modal" data-target="#confirmStopModal">
                                    <button type="button" data-toggle="tooltip" data-placement="top"
                                            title="Stop Automatic Recurring Payments" class="btn btn-cancel btn-sm mr-3" {{ $lease->trashed() || session('adminLoginAsUser') ? 'disabled' : '' }}><i
                                            class="fas fa-ban mr-1"></i> Stop Recurring Payments</button>
                                </span>
                            </div>
                        </div>
                    </form>
                @else
                    @if(!session('adminLoginAsUser'))
                        <div class="card-footer">
                            <div class="text-right">
                                @if($lease->trashed())
                                    <button class="btn btn-primary btn-sm" disabled><i
                                            class="fal fa-stopwatch mr-2"></i> Setup Recurring Payments
                                    </button>
                                @elseif ($lease->landlordLinkedFinance())
                                    <button
                                        onclick="window.location.href = '{{ route('recurring-setup',['lease'=>$lease->id]) }}'"
                                        class="btn btn-primary btn-sm"><i class="fal fa-stopwatch mr-2"></i> Setup
                                        Recurring Payments
                                    </button>
                                @else
                                    <span class="d-inline-block" data-toggle="tooltip" data-placement="top"
                                          title="Please contact your landlord">
                                        <button class="btn btn-primary btn-sm disabled"><i
                                                class="fal fa-stopwatch mr-2"></i> Setup Recurring Payments</button>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

            @else
                <!-- Landlord -->

            @endif
        </div>
    </div>

    @if (!$user->isTenant())
        <div class="collapse multi-collapse" id="updateFinanceAccountContent">
            <form method="post"
                  action="{{ route('properties/units/payments/change-finance-account',['unit' => $lease->unit_id]) }}">
                @csrf

                <div class="card-footer text-muted bg-light">
                    <input type="hidden" name="financeAccount" id="financeAccountDupl"
                           value="{{ $financeAccount ? $financeAccount->id : '' }}">
                    <input type="hidden" name="unit_id" value="{{ $lease->unit_id }}">
                    <input type="hidden" name="lease_id" value="{{ $lease->id }}">

                    <a data-toggle="collapse" href="#updateFinanceAccountContent" aria-expanded="true"
                       aria-controls="updateFinanceAccountContent"
                       class="btn btn-cancel btn-sm mr-3 cancelChangeFinancial"><i class="fal fa-times mr-1"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm float-right"><i
                            class="fal fa-check-circle mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
        <div class="collapse multi-collapse" id="addFinanceAccountContent">
            <form method="post"
                  action="{{ route('properties/units/payments/change-finance-account',['unit' => $lease->unit_id]) }}">
                @csrf
                <div class="card-header bg-light border-top financeSwitchHeader">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="financeSwitch1" name="financeSwitch" class="custom-control-input"
                               value="stripe" checked>
                        <label class="custom-control-label" for="financeSwitch1"><i class="fab fa-cc-stripe"></i>
                            Connect Stripe Account</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="financeSwitchDwolla" name="financeSwitch" class="custom-control-input"
                               value="dwolla_target">
                        <label class="custom-control-label" for="financeSwitchDwolla"><i
                                class="fa fa-envelope-open-dollar"></i> Receive ACH Payments</label>
                    </div>
                </div>
                <div class="financeSwitchContent" id="financeSwitchContent1">
                    <div class="card-body">
                        <input type="hidden" name="financeAccount" value="_new">
                        <input type="hidden" name="lease_id" value="{{ $lease->id }}">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="holderName">Holder Name <i class="required fal fa-asterisk"></i></label>
                                <input type="text"
                                       class="form-control @error('account_holder_name') is-invalid @enderror"
                                       name="account_holder_name" id="holderName"
                                       value="{{ old('account_holder_name') }}" maxlength="64">
                                @error('account_holder_name')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="financeAccountNickname">Financial Account Nickname <i
                                        class="required fal fa-asterisk"></i></label>
                                <input type="text" class="form-control @error('nickname') is-invalid @enderror"
                                       name="nickname" id="financeAccountNickname" value="{{ old('nickname') }}"
                                       maxlength="32">
                                @error('nickname')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="stripeAccount">Stripe Account ID <i
                                        class="required fal fa-asterisk"></i></label>
                                <input type="text" class="form-control @error('stripe_account_id') is-invalid @enderror"
                                       name="stripe_account_id" id="stripeAccount"
                                       value="{{ old('stripe_account_id') }}" maxlength="64">
                                @error('stripe_account_id')
                                <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="financeSwitchContent" id="financeSwitchContentDwolla" style="display: none">
                    @php
                        $identity = \App\Models\UserIdentity::where('user_id', Auth::user()->id)->first();
                    @endphp
                    {{--}}@if(!empty($identity)){{--}}


                    <div class="card-body">
                        <div class="h3 text-warning">Under Construction</div>
                    </div>


                    {{--}}
                    <!-- TODO Archive DWOLLA integration (not remove) -->

                        <div class="card-body">
                            @if (session('dwolla-error'))
                                <div class="customFormAlert alert alert-danger" role="alert">
                                    {!! session('dwolla-error') !!}
                                </div>
                            @endif

                            <div class="inRowComment text-primary2">
                                <div><i class="fas fa-exclamation-circle text-primary2"></i> Please ensure that Holder name is spelled exactly as it appears on your banking information.</div>
                                <div class="pl-3">FREE for the landlord. Your tenant will pay a maximum $5.00 per transaction.</div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="holderName">Holder Name <i class="required fal fa-asterisk"></i></label>
                                    <input type="text" class="form-control @error('dwolla_account_holder_name') is-invalid @enderror" name="dwolla_account_holder_name" id="holderName" value="{{ old('dwolla_account_holder_name') ?? (!empty($identity) ? ($identity->first_name . " " . $identity->last_name) : '') }}" maxlength="64">
                                    @error('dwolla_account_holder_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="dwolla_account_holder_name-error"></strong>
                                                </span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="routingNumber">Routing Number <i class="required fal fa-asterisk"></i></label>
                                    <input type="text" class="form-control @error('dwolla_routing_number') is-invalid @enderror" name="dwolla_routing_number" id="routingNumber" value="{{ old('dwolla_routing_number') }}" data-type="integer" maxlength="9">
                                    @error('dwolla_routing_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="dwolla_routing_number-error"></strong>
                                    </span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="accountNumber">Account Number <i class="required fal fa-asterisk"></i></label>
                                    <input type="text" class="form-control @error('dwolla_account_number') is-invalid @enderror" name="dwolla_account_number" id="accountNumber" value="{{ old('dwolla_account_number') }}" data-type="integer" maxlength="17">
                                    @error('dwolla_account_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="dwolla_account_number-error"></strong>
                                    </span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="bankAccountType">Bank Account Type <i class="required fal fa-asterisk"></i></label>
                                    <select class="form-control @error('bank_account_type') is-invalid @enderror" name="dwolla_bank_account_type" id="bankAccountType">
                                        <option value="checking" @if(old('dwolla_bank_account_type') == 'checking') selected @endif >Checking</option>
                                        <option value="savings" @if(old('dwolla_bank_account_type') == 'savings') selected @endif >Savings</option>
                                    </select>
                                    @error('dwolla_bank_account_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="dwolla_bank_account_type_confirmation-error"></strong>
                                    </span>
                                </div>
                            </div>

                            @if(empty(Auth::user()->dwolla_tos))
                                <div>
                                    <div class="custom-control custom-checkbox custom-control-inline pr-4 primary-border-checkbox">
                                        <input
                                                type="checkbox"
                                                class="custom-control-input @error('accept_tos') is-invalid @enderror"
                                                name="accept_tos"
                                                value="1"
                                                id="accept_tos"
                                                @if(old('accept_tos') == '1') checked @endif
                                        >
                                        <label
                                                class="custom-control-label d-block"
                                                for="accept_tos"
                                        >
                                            By checking this box you agree to our partner <a target="_blank" href="https://www.dwolla.com/legal/tos/" class="text-primary2">Dwolla's Terms of Service</a> and <a target="_blank" href="https://www.dwolla.com/legal/privacy/" class="text-primary2">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                            @endif

                        </div>
                    {{--}}




                    {{--}}@else
                        <div class="card-body">
                            <div class="alert alert-warning mb-0" role="alert">
                                <p>You will be eligible to use this feature after the successful user verification.</p>
                                <div>
                                    <a class="btn btn-sm btn-primary" href="{{route("profile/identity")}}"><i class="fal fa-shield-alt mr-1"></i> Process User Verification</a>
                                </div>
                            </div>
                        </div>
                    @endif{{--}}

                </div>


                <div class="card-footer text-muted bg-light">
                    <a data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="true"
                       aria-controls="addFinanceAccountContent"
                       class="btn btn-cancel btn-sm mr-3 cancelChangeFinancial"><i class="fal fa-times mr-1"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm float-right">
                        <i class="fal fa-check-circle mr-1"></i>
                        @if(empty(Auth::user()->dwolla_tos))
                            Agree and Save
                        @else
                            Save Financial Account
                        @endif
                    </button>
                </div>
            </form>

        </div>
    @endif

    @if (!$user->isTenant() && (!$lease->end_date || ($lease->end_date > now())))

        <div class="card-header border-top">
            Request Payment from your tenant
        </div>
        <form class="checkUnload" method="POST" action="{{ route('add-bill') }}" enctype="multipart/form-data">
            @csrf
            <div class="p-3 bg-light">

                <div class="inRowComment">
                    <i class="fal fa-info-circle"></i>
                    If tenant is registered in MYNESTHUB, our platform will send an email notification to the tenant
                    about added payment. Tenant will also have an ability to view given payment on our platform.
                </div>
                <input type="hidden" name="lease_id" value="{{ $lease->id }}">
                <input type="hidden" name="unit_id" value="{{ $lease->unit->id }}">
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="billType">Payment Type <i class="required fal fa-asterisk"></i></label>
                        <select name="bill_type"
                                class="custom-select form-control @error('bill_type') is-invalid @enderror @error('bill_name') is-invalid @enderror"
                                id="billType">
                            <option value="">Select Payment Type</option>
                            @foreach ($defaultBills as $item)
                                <option
                                    value="{{ $item->id }}" {{ old('bill_type') && old('bill_type') ==  $item->id ? 'selected' : '' }}>{{ ucfirst(strtolower($item->name)) }}</option>
                            @endforeach
                            <option
                                value="_new" {{ old('bill_type') && old('bill_type') ==  '_new' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>
                        <div style="display: none" class="input-group" id="billTypeOtherBox">
                            <input id="billTypeOther" type="text" name="bill_name" class="form-control"
                                   placeholder="Enter Bill Type" aria-label="Enter Bill Type"
                                   value="{{ old('bill_name') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="billTypeCancel"><i
                                        class="fal fa-times"></i></button>
                            </div>
                        </div>
                        @error('bill_type')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        @error('bill_name')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bill_amount">Amount <i class="required fal fa-asterisk"></i></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">$</div>
                            </div>
                            <input type="text" class="form-control @error('bill_amount') is-invalid @enderror"
                                   name="bill_amount" id="bill_amount" data-type="currency" maxlength="10"
                                   value="{{ old('bill_amount') ?? '' }}">
                            @error('bill_amount')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bill_due">Due Date <i class="required fal fa-asterisk"></i></label>
                        <input name="bill_due" id="bill_due" type="date"
                               value="{{ old('bill_due') ? old('bill_due') : \Carbon\Carbon::now()->addDays(5)->format('Y-m-d') }}"
                               class="form-control @error('bill_due') is-invalid @enderror">
                        @error('bill_due')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bill_file">Document</label>

                        <div class="custom-file customSingleFile">
                            <input type="file" class="custom-file-input" id="bill_file" name="bill_file">
                            <label for="bill_file" class="custom-file-label" data-browse="">Upload File</label>
                        </div>

                    </div>
                </div>
            </div>
            <div class="p-3 bg-white text-muted border-top text-right">
                <button class="btn btn-primary btn-sm"><i class="fal fa-check-circle mr-1"></i> Request Payment</button>
            </div>
        </form>
    @endif
</div>

<a name="invoices"></a>
<div id="invoicesBox"></div>

<!-- STOP recurring payments dialog-->
<div class="modal fade" id="confirmStopModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Stop Recurring Payments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <p>We will immediately stop all of upcoming payments. Any payments that have already started will
                    continue to process.</p>
                <div>Do you want to proceed?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary mr-2" data-dismiss="modal">Don't Change</button>
                <button type="button" class="btn btn-sm btn-danger btn-ok btn-stop"><i class="fal fa-ban mr-1"></i> Stop
                    Payments
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE RECORD confirmation dialog-->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <p>You are about to delete <b><i class="title"></i></b>, this procedure is irreversible.</p>
                <div>Do you want to proceed?</div>
            </div>
            <div class="modal-footer">
                <form class="remove-invoice" method="POST" action="{{ route('remove-invoice') }}">
                    @csrf
                    <input type="hidden" name="invoice_id" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-ok">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@if (!$user->isTenant())
    @if (($selectedLease !== false) && empty($selectedLease->deleted_at))
        <!-- Resend Email confirmation dialog-->
        <div class="modal fade" id="confirmResendEmailModal" tabindex="-1" role="dialog"
             aria-labelledby="confirmResendEmailModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmResendEmailModalTitle">Resend an Invitation Email</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <p class="mb-0">Do You want to resend an invitation email to <strong
                                id="modalFirstname">{!! $selectedLease->firstname !!}</strong> <strong
                                id="modalLastname">{!! $selectedLease->lastname !!}</strong>?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i
                                class="fal fa-times mr-1"></i> Cancel
                        </button>
                        <form method="POST" action="{{ route('leases/resend-email') }}">
                            @csrf
                            <input type="hidden" name="lease" value="{{ $selectedLease->id }}">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-paper-plane mr-2"></i>
                                Resend an Invitation Email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@section('scripts_in_modules')
    <script>
        $(document).ready(function () {
            $('input[name="last12Month"]').change(function () {
                var val = $('input[name="last12Month"]:checked').val();
                if (val == 1) {
                    $('.last12Month1').removeClass('d-none');
                    $('.last12Month0').addClass('d-none');
                } else {
                    $('.last12Month0').removeClass('d-none');
                    $('.last12Month1').addClass('d-none');
                }
            });
            $('#last12Month1').click();

            $('#addAccBut').click(function () {
                $("#financeAccount").val("_new");
                $("#financeAccountDupl").val("_new");
                $("#addFinanceAccountContent").addClass("show");
            });
            $("#financeAccount").change(function () {
                var val = $(this).val();
                if (val === "_new") {
                    $("#addFinanceAccountContent").collapse('show');
                    $("#updateFinanceAccountContent").collapse('hide')
                } else {
                    $("#addFinanceAccountContent").collapse('hide');

                    if ($(this).val() != $(this).data('current')) {
                        $("#updateFinanceAccountContent").collapse('show')
                    } else {
                        $("#updateFinanceAccountContent").collapse('hide')
                    }
                }
                $("#financeAccountDupl").val(val);
            });
            $("#financeAccount").val($("#financeAccount").data('current'));
            $('.cancelChangeFinancial').on('click', function (event) {
                $("#financeAccount").val($("#financeAccount").data('current'));
                $("#financeAccountDupl").val($("#financeAccount").data('current'));
            });
            if ($("#addFinanceAccountContent").find(".is-invalid").length > 0) {
                $("#addFinanceAccountContent").addClass("show");
                $("#financeAccount").val("_new");
                $("#financeAccountDupl").val("_new");
            }


            //$('body').tooltip({
            //    selector: '.withTooltip'
            //});
        });

        jQuery(document).ready(function () {
            if (window.location.hash) {
                $('#addFinanceAccountContent').removeClass('collapse');
                var activateElement;
                switch (window.location.hash) {
                    case '#stripe':
                        activateElement = $('input[type="radio"][value="stripe"]').first();
                        break;
                    case '#ach':
                        activateElement = $('input[type="radio"][value="dwolla_target"]').first();
                        break;
                }
                activateElement.prop('checked', true);
                switchAccountForm(activateElement.val());
            } else {
                $('input[type="radio"][name="financeSwitch"]').first().prop('checked', true);
            }

            if ($('#financeSwitchContentDwolla .is-invalid, #financeSwitchContentDwolla .customFormAlert').length > 0) {
                $('#addFinanceAccountContent').removeClass('collapse');
                $('#financeSwitchContent1').hide();
                $('#financeSwitchContentDwolla').show();
                $('input[type="radio"][value="dwolla_target"]').prop('checked', true);
            }
            $('input[type=radio][name=financeSwitch]').change(function () {
                switchAccountForm(this.value);
            });
        });

        function switchAccountForm(accountType) {
            if (accountType == 'stripe') {
                $('#financeSwitchContent1').show();
                $('#financeSwitchContentDwolla').hide();
                window.location.hash = 'stripe';
            } else if (accountType == 'dwolla_target') {
                $('#financeSwitchContent1').hide();
                $('#financeSwitchContentDwolla').show();
                window.location.hash = 'ach';
            }
        }

    </script>
@endsection
