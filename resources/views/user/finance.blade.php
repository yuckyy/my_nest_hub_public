@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/finance') }}">Financial Account</a>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">My Account</h1>
                    <h6 class="text-center text-sm-left pb-3 text-secondary">
                        <a href="{{ route('profile') }}">{{ Auth::user()->fullName() }}</a>
                    </h6>
                </div>
            </div>

            @if (session('general-error'))
                <div class="customFormAlert alert alert-danger" role="alert">
                    {!! session('general-error') !!}
                </div>
            @endif

        </div>

        <div class="container-fluid unitFormContainer">
            {{--@if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif--}}
            <div class="row">

                @include('includes.user.account-menu',['active' => 'finance'])

                <div class="profileNavTabsLeftContent col-md-9">

                    <div class="card propertyForm propertyFormGeneralInfo">
                        @if (!session('adminLoginAsUser'))

                        <div class="card-header">
                            <span>Financial Accounts</span>
                        </div>

                        <div class="card-body bg-light">
                            <div class="financeAccountsList mb-3">

                                @foreach ($user->financialAllAccounts() as $f)
                                    <div class="card financeAccountCard">
                                        <div class="card-body p-2">
                                            <div class="financeAccountCardIconSell text-center">
                                                {!! financialTypeIcon($f->finance_type) !!}
                                            </div>
                                            <div class="financeAccountCardBody">
                                                <div class="ml-2 card-text">
                                                    <div class="ml-2 card-text">
                                                        @if($f->finance_type == 'paypal')
                                                            {{ $f->paypal_email }}
                                                        @else
                                                            {{ $f->nickname }} @if($f->finance_type == 'stripe_account') <span class="badge badge-{{ $f->finance_units->count() > 0 ? 'success' : 'light' }}">{{ $f->finance_units->count() ? 'Linked to Units' : 'Not Linked' }}</span> @endif
                                                            @if ($f->finance_units->count())
                                                                <a href="#" title="View Units" data-toggle="modal" data-target="#viewUnitsModal" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="{{ financialTypeIconClassSmall($f->finance_type) }}" class="financeAccountCardAction btn btn-sm btn-light text-muted">
                                                                    <i class="fal fa-link"></i><span> View Units</span>
                                                                </a>
                                                            @endif
                                                            @if(!$user->isTenant() && ($f->finance_type == 'dwolla_target'))
                                                                @if($f->connected == 0)
                                                                    <div class="d-inline-block ml-1 mr-auto" data-toggle="tooltip" data-placement="top" title="Your Identity didn't verified yet. Please Verify">
                                                                        <a href="{{ route('profile/identity') }}" class="btn btn-light btn-sm text-danger">
                                                                            <i class="fas fa-exclamation-triangle text-danger"></i>
                                                                            @if(
                                                                                ($user->userIdentityStatus() == 'retry') || ($user->userIdentityStatus() == 'document')
                                                                            )
                                                                                Your identity verification attempt was unsuccessful, Verify your identity.
                                                                            @else
                                                                                Verify Your Identity
                                                                            @endif
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <div class="d-inline-block ml-1 mr-auto" data-toggle="tooltip" data-placement="top" title="View Your Identity Information">
                                                                        <a href="{{ route('profile/identity') }}" class="btn btn-light btn-sm text-primary2">
                                                                            <i class="fas fa-info-circle text-primary2"></i> View Identity Info
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="ml-2">
                                                        @if($f->finance_type == 'paypal')
                                                        @elseif($f->finance_type == 'dwolla_source')
                                                        @else
                                                            <span class="mr-2 text-secondary">{{ $f->finance_type == 'card' ? '****-****-****-' . $f->last4 : '***-' . $f->last4 }}{{ $f->exp_date ? ', EXP' .  $f->exp_date : '' }}</span>
                                                        @endif
                                                        <span class="text-secondary d-none d-lg-inline">{{ $f->finance_type == 'card' ? 'Issued To: ' . $f->holder_name . ', ' . $f->full_address : $f->holder_name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="financeAccountCardNav">
                                                @if(!$user->isTenant() && $f->finance_type == 'stripe_account')
                                                    @if ($user->financialStripeAccounts()->count() > 1 && $f->finance_units->count())
                                                        <span data-toggle="modal" data-target="#replaceModal" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="fa-university" data-finance_type="{{ $f->finance_type }}">
                                                            <a href="#" data-toggle="tooltip" data-placement="top" title="Replace Payment Method" class="btn btn-sm btn-light mr-1 text-secondary"><i class="fal fa-exchange"></i><span class="d-lg-none"> Replace</span></a>
                                                        </span>
                                                    @endif
                                                    <span id="activateLinkUnits{{ $f->id }}" data-toggle="modal" data-target="#viewUnitsModal" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="{{ financialTypeIconClassSmall($f->finance_type) }}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="View Linked Units" class="btn btn-sm btn-light mr-1 text-success"><i class="fal fa-link"></i><span class="d-lg-none"> View Units</span></a>
                                                    </span>
                                                @endif
                                                @if(!$user->isTenant() && $f->finance_type == 'dwolla_target')
                                                    @if ($user->financialDwollaAccounts()->count() > 1 && $f->finance_units->count())
                                                        <span data-toggle="modal" data-target="#replaceModal" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="{{ financialTypeIconClassSmall($f->finance_type) }}" data-finance_type="{{ $f->finance_type }}">
                                                            <a href="#" data-toggle="tooltip" data-placement="top" title="Replace Payment Method" class="btn btn-sm btn-light mr-1 text-secondary"><i class="fal fa-exchange"></i><span class="d-lg-none"> Replace</span></a>
                                                        </span>
                                                    @endif
                                                    <span id="activateLinkUnits{{ $f->id }}" data-toggle="modal" data-target="#viewUnitsModal" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="{{ financialTypeIconClassSmall($f->finance_type) }}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="View/Link Units" class="btn btn-sm btn-light mr-1 text-success"><i class="fal fa-link"></i><span class="d-lg-none"> View/Link Units</span></a>
                                                    </span>
                                                @endif
                                                <span data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="{{ financialTypeIconClassSmall($f->finance_type) }}">
                                                    <a href="#" data-toggle="tooltip" data-placement="top" title="Delete Payment Method" class="btn btn-sm btn-light mr-1 text-danger"><i class="fal fa-trash-alt"></i><span class="d-lg-none"> Delete</span></a>
                                                </span>

                                                {{--}}@if($f->finance_type == 'card' || $f->finance_type == 'bank'){{--}}
                                                    <span data-toggle="modal" data-target="#{{ $f->finance_type == 'card' ? 'editCardModal' : 'editBankModal' }}" data-record-id="{{ $f->id }}" data-record-title="{{ $f->nickname }}" data-record-icon="{{ financialTypeIconClassSmall($f->finance_type) }}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Edit Payment Method" class="btn btn-sm btn-light mr-1 text-secondary"><i class="fal fa-cog"></i><span class="d-lg-none"> Edit</span></a>
                                                    </span>
                                                {{--}}@endif{{--}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="inRowComment"><i class="fal fa-info-circle"></i> Link a checking account or debit card to {{ $user->isTenant() ? 'send' : 'receive' }} your rent and one-time payments.</div>
                            <div class="addBillBox mb-3">
                                @if (Auth::user()->isTenant())
                                    @if(!empty($lease) && $lease->landlordLinkedFinance())
                                        <a id="addFinanceAccountButton" data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="false" aria-controls="addFinanceAccountContent" class="btn btn-outline-secondary btn-sm collapsed"><i class="fal fa-plus-circle mr-1"></i><i class="fal fa-minus-circle mr-1"></i>Add Financial Account</a>
                                    @else
                                        <span class="d-inline-block" data-toggle="tooltip" data-placement="top" title="Please contact your landlord">
                                            <a href="#" class="btn btn-outline-secondary btn-sm disabled"><i class="fal fa-plus-circle mr-1"></i>Add Financial Account</a>
                                        </span>
                                    @endif
                                @else
                                    <a id="addFinanceAccountButton" data-toggle="collapse" href="#addFinanceAccountContent" aria-expanded="false" aria-controls="addFinanceAccountContent" class="btn btn-outline-secondary btn-sm collapsed"><i class="fal fa-plus-circle mr-1"></i><i class="fal fa-minus-circle mr-1"></i>Add Financial Account</a>
                                    {{--<img class="d-block d-sm-inline-block mt-3 ml-sm-3 mt-sm-0" src="{{ url('/') }}/images/Powered-by-Stripe-blurple.png" height="24" alt="Powered by Stripe">--}}
                                @endif
                            </div>
                        </div>
                        <div class="collapse multi-collapse" id="addFinanceAccountContent">
                            @if (Auth::user()->isTenant())
                                @component('includes.finance.add_finance_account_form_tenant', ['identity' => $identity, 'lease' => $lease])
                                @endcomponent
                            @else
                                @component('includes.finance.add_finance_account_form_landlord', ['identity' => $identity, 'lease' => $lease])
                                @endcomponent
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="loadingPreload"></div>

    <!-- DELETE RECORD confirmation dialog-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form action="{{ route('remove-finance-account') }}" method="post">
                    @csrf
                    <input type="hidden" name="record_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalTitle">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <p>You are about to delete <span class="paymentIcon"><i></i></span> <strong class="paymentNickname">-</strong>, this procedure is irreversible.</p>
                        <div>Do you want to proceed?</div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger btn-ok" id="confirmDeleteModalSubmit"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- LINKED UNITS dialog-->
    <div class="modal fade" id="viewUnitsModal" tabindex="-1" role="dialog" aria-labelledby="viewUnitsModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUnitsModalTitle">Linked Units</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('link-units') }}" method="post">
                    @csrf
                    <div class="modal-body bg-light">
                        <div id="linked-units">
                            @error('linked_id')
                            @if (isset($finance))
                                @include('includes.finance.linked-units-modal')
                            @endif
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
                        @if (isset($units) && count($units) > 0)
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Link Units</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT BANK PAYMENT dialog-->
    <div class="modal fade" id="editBankModal" tabindex="-1" role="dialog" aria-labelledby="editBankModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBankModalTitle">Edit Financial Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('update-finance-account') }}" method="post">
                    @csrf
                    <div class="modal-body bg-light">
                        <div id="edit-bank-form">
                            @if (isset($finance) && $finance->finance_type == 'bank')
                                @include('includes.finance.edit-modal')
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT CARD PAYMENT dialog-->
    <div class="modal fade" id="editCardModal" tabindex="-1" role="dialog" aria-labelledby="editCardModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCardModalTitle">Edit Financial Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('update-finance-account') }}" method="post">
                    @csrf
                    <div id="editFinanceAccountModalForm">
                        <div class="modal-body bg-light">
                            <div id="edit-card-form">
                                @if (isset($finance) && $finance->finance_type == 'card')
                                    @include('includes.finance.edit-modal')
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- REPLACE FINANCIAL ACCOUNT dialog-->
    <div class="modal fade" id="replaceModal" tabindex="-1" role="dialog" aria-labelledby="replaceModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form action="{{ route('replace-finance-account') }}" method="post">
                    @csrf
                    <input type="hidden" name="record_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="replaceModalTitle">Replace Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <p class="card-text">
                                    <i class="fal fa-info-circle"></i> Replacing this account with another will transfer all of your linked units payments to the new account.
                                </p>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-md-12">
                                <label for="financeAccount">Replace <span class="paymentIcon"><i></i></span> <strong class="paymentNickname">-</strong> with</label>
                                <select name="replace_with" class="custom-select fixedMaxInputWidth" id="financeAccount" required>
                                    <option value="" hidden>Choose an account</option>
                                    @foreach ($user->financialAccounts as $f)
                                        <option value="{{ $f->id }}" data-finance_type="{{ $f->finance_type }}">{{ $f->nickname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Replace Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if (session('show-linked-units-message'))
        <div class="modal fade" id="successAddedFinanceModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-center m-auto text-white"><i class="fas fa-thumbs-up mr-3"></i>Success</h5>
                    </div>
                    <form action="{{ route('link-units') }}" method="post">
                        @csrf
                        <div class="modal-body bg-light">
                            <h6 class="text-center">{!! session('show-linked-units-message') !!}</h6>

                            <!-- whats next -->
                                <div class="alert alert-success mt-3 mb-0" role="alert">
                                    <h4 class="alert-heading"><i class="fal fa-walking mr-1"></i> What's Next?</h4>
                                    <hr>
                                    <p class="mb-0">
                                        Now it’s time to link units that you want to associate with this financial account. If current/future tenants decide to setup their financial account on our system then our system will forward you all recurring/one-time payments from those tenants on this account.
                                    </p>
                                </div>

                            <p class="text-center pt-3 mb-2">
                                Check/Uncheck checkbox to associate/de-associate unit(s) associated with this financial account and press Save to save your changes.
                            </p>
                            <div id="linked-units-2"></div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
                            <button type="submit" class="btn btn-sm btn-secondary"><i class="fal fa-check-circle mr-1"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        if ($('#addFinanceAccountContent .is-invalid, #financeSwitchContentDwolla .customFormAlert').length > 0) {
            $('#addFinanceAccountContent').collapse();
        }
        $('.modal').each(function () {
            if ($(this).find('.is-invalid').length > 0) {
                $(this).modal('show');
            }
        });
        $('#confirmDeleteModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('.paymentNickname').text(t.data('record-title'));
            $(this).find('.paymentIcon').find("i").removeClass().addClass(t.data('record-icon'));
            // $(this).find('.btn-ok').data('record-id', t.data('record-id'));
            $(this).find('input[name=record_id]').val(t.data('record-id'));
        });

        $('#replaceModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('input[name=record_id]').val(t.data('record-id'));
            $(this).find('option').hide();
            $(this).find('option[data-finance_type='+t.data('finance_type')+']').show();
            $(this).find('option[value='+t.data('record-id')+']').hide();
        });

        $('#editBankModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('input[name=record_id]').val(t.data('record-id'));
            $.post("{{ route('edit-finance-account') }}", {
                record_id: t.data('record-id'),
            }, function(datajson){
                $('#edit-bank-form').html(datajson.view);
                $('#edit-bank-form').find('.paymentIcon').find("i").removeClass().addClass(t.data('record-icon'));
            });
        });

        $('#editCardModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('input[name=record_id]').val(t.data('record-id'));
            $.post("{{ route('edit-finance-account') }}", {
                record_id: t.data('record-id'),
            }, function(datajson){
                $('#edit-card-form').html(datajson.view);
                $('#edit-bank-form').find('.paymentIcon').find("i").removeClass().addClass(t.data('record-icon'));
            });
        });

        $('#viewUnitsModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('input[name=record_id]').val(t.data('record-id'));
            $.post("{{ route('get-linked-units') }}", {
                record_id: t.data('record-id'),
            }, function(datajson){
                $('#linked-units').html(datajson.view);
                $('#viewUnitsModal').find('.paymentIcon').find("i").removeClass().addClass(t.data('record-icon'));
            });
        });

        $("#confirmDeleteModalSubmit").on('click',function(e){
            $('#confirmDeleteModal').modal('hide');
            $(".preloader").fadeIn("fast");
        });

        $('form').on('submit', function(e){
            var elm = $(this).find('[name="paypal_email"]');
            if(elm.length == 0){
                return true;
            }
            var paypal_email = elm.first();
            if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(paypal_email.val())){
                return true;
            }
            e.preventDefault();
            paypal_email.addClass('is-invalid');
            return false;
        });

        function ajaxSubmit(form) {
            $(form).find('.invalid-feedback strong').html('');
            $(form).find('.error-message').html('');
            $('.form-control').removeClass('is-invalid');
            $(form).find('.btn-submit').prop('disabled',true);
            $(".preloader").fadeIn("slow");
            $.ajax({
                url     : $(form).find('input[name=form-action]').val(),
                type    : $(form).attr('method'),
                data    : $(form).serialize(),
                dataType: 'json',
                success : function (json) {
                    $(form).find('.btn-submit').prop('disabled',false);
                    $(".preloader").fadeOut("slow");
                    window.location.href = json.route;
                },
                error: function(json) {
                    $(form).find('.btn-submit').prop('disabled',false);
                    $(".preloader").fadeOut("slow");
                    $(form).find('.invalid-feedback strong').html('');
                    $(form).find('.error-message').html('');
                    $('.form-control').removeClass('is-invalid');
                    if(json.status === 422) {
                        var errors = json.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('.'+key+'-error').closest('div').find('.form-control').addClass('is-invalid');
                            $('.'+key+'-error').html(value);
                        });
                    } else {
                        $(form).find('.invalid-feedback strong').html('');
                        $('.form-control').removeClass('is-invalid');
                        $(form).find('.error-message').html(json.responseJSON.message);
                    }
                }
            });
        }

        function ajaxSubmitConnectedForm(form) {
            console.log('Connect: ajax account id Submit');

            $(form).find('.invalid-feedback strong').html('');
            $(form).find('.error-message').html('');
            $(".preloader").fadeIn("slow");
            $.ajax({
                url     : $(form).find('input[name=form-action]').val(),
                type    : $(form).attr('method'),
                data    : $(form).serialize(),
                dataType: 'json',
                success : function (json) {

                    console.log('Connect: ajax account id Submit success');

                    $(".preloader").fadeOut("slow");
                    window.location.href = json.route;
                },
                error: function(json) {
                    $(".preloader").fadeOut("slow");
                    $(form).find('.error-message').html(json.responseJSON.message);
                }
            });
        }
    </script>
    @if (session('show-linked-units-message'))
        <script>
            $(document).ready(function() {
                $('#successAddedFinanceModal').modal('show');
            });
            $('#successAddedFinanceModal').on('show.bs.modal', function(event) {
                $(this).find('input[name=record_id]').val('{{ \Request::get('finance_id') }}');
                $.post("{{ route('get-linked-units') }}", {
                    record_id: '{{ \Request::get('finance_id') }}',
                }, function(datajson){
                    $('#linked-units-2').html(datajson.view);
                    $('#successAddedFinanceModal').find('.linkedUnitsHeader').hide();
                });
            });
        </script>
    @endif
@endsection
