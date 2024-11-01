@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="#">Lease</a> > <a href="#">Create Lease</a>
    </div>
    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <div class="d-block d-md-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <h1 class="h2 text-center text-sm-left">Create Lease</h1>
                <ul id="progressbar">
                    <li class="active progress1"><div>Tenant and Property</div><div>Information</div></li>
                    <li class="active progress2"><div>Extra Fees and</div><div>Assistance</div></li>
                    <li class="active progress3"><div>Payments and</div><div>Documents</div></li>
                </ul>
            </div>
        </div>

        <div class="container-fluid">

            <form class="needs-validation finance-form checkUnloadByDefoult" novalidate method="POST" action="{{ route('leases/add') }}">
                @csrf

                <input type="hidden" name="step" id="step" value="{{ $step }}">

                <div class="card propertyForm">

                    <div class="card-header">
                        Where should we send payments?
                    </div>
                    <div class="card-body bg-light pb-2">
                        <div class="inRowComment"><i class="fal fa-info-circle"></i> Select your financial account to receive your rent and one-time payments.</div>
                        <div class="mb-3">
                            <div class="d-inline-block">
                                <label for="financeAccount">Financial Account <i class="required fal fa-asterisk"></i></label>
                                <select name="financeAccount" class="form-control @error('financeAccount') is-invalid @enderror custom-select fixedMaxInputWidth" id="financeAccount" required>
                                    <option value="" hidden>Choose an account</option>
                                    @foreach (Auth::user()->financialCollectRecurringAccounts() as $f)
                                        <option value="{{ $f->id }}" @if(!empty($data['financeAccount']) && ($data['financeAccount'] == $f->id)) selected @endif>{{ $f->nickname }}</option>
                                    @endforeach
                                    <option @if(!empty($data['financeAccount']) && ($data['financeAccount'] == "other")) selected @endif value="other">Other (check, cache, manual transaction)</option>
                                    <option @if((!empty($data['financeAccount']) && ($data['financeAccount'] == "_new")) || (empty($f) && empty($data['financeAccount']))) selected @endif value="_new">Add Financial Account</option>
                                </select>
                                @error('financeAccount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            {{--}}<img class="d-block d-sm-inline-block mt-3 ml-sm-3 mt-sm-0" src="{{ url('/') }}/images/Powered-by-Stripe-blurple.png" height="24" alt="Powered by Stripe">{{--}}
                        </div>
                    </div>

                    <div class="collapse multi-collapse" id="addFinanceAccountContent">

                        <div class="card-header border-top financeSwitchHeader">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="financeSwitch1" name="financeSwitch" class="custom-control-input" value="stripe" checked>
                                <label class="custom-control-label" for="financeSwitch1"><i class="fab fa-cc-stripe"></i> Connect Stripe Account</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="financeSwitchDwolla" name="financeSwitch" class="custom-control-input" value="dwolla_target">
                                <label class="custom-control-label" for="financeSwitchDwolla"><i class="fa fa-envelope-open-dollar"></i> Receive ACH Payments</label>
                            </div>
                        </div>
                        <div class="financeSwitchContent" id="financeSwitchContent1">
                            <div class="card-body">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="holderName">Holder Name <i class="required fal fa-asterisk"></i></label>
                                        <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" name="account_holder_name" id="holderName" value="{{ old('account_holder_name') }}" maxlength="64">
                                        @error('account_holder_name')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="financeAccountNickname">Financial Account Nickname <i class="required fal fa-asterisk"></i></label>
                                        <input type="text" class="form-control @error('nickname') is-invalid @enderror" name="nickname" id="financeAccountNickname" value="{{ old('nickname') }}" maxlength="32">
                                        @error('nickname')
                                            <span class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="stripeAccount">Stripe Account ID <i class="required fal fa-asterisk"></i></label>
                                        <input type="text" class="form-control @error('stripe_account_id') is-invalid @enderror" name="stripe_account_id" id="stripeAccount" value="{{ old('stripe_account_id') }}" maxlength="64">
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
                                                @error('accept_tos')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>Please agree with Dwolla's Terms of Service and Privacy Policy</strong>
                                                </span>
                                                @enderror
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

                    </div>
                </div>
                <div class="card propertyForm mt-4">

                    <div class="row no-gutters">
                        <div class="col-md-6 bg-light border-right">

                            <div class="card-header">
                                Share any additional documents
                            </div>
                            <div class="card-body bg-light">
                                <div class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                    <div class="filesBox">
                                        <div class="h1 pb-1">
                                            <i class="fal fa-file-alt"></i>
                                        </div>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="documentUpload" multiple>
                                        <div class="custom-file-label btn btn-sm btn-primary" for="documentUpload" data-browse=""><i class="fal fa-upload"></i> Upload Documents</div>
                                    </div>
                                    <small>
                                        Upload here the attachments that you want to be visible to your tenants.<br>
                                        Maximum size: 2Mb.<br>
                                        Allowed file types: doc, pdf, txt, jpg, png, gif, xls, csv.
                                    </small>
                                </div>
                                <div class="pt-3 @if($documents->count() == 0) d-none @endif ">
                                    <ul id="sharedFileList" class="sharedFileList list-group">
                                        @foreach ($documents as $document)
                                            <li class="list-group-item list-group-item-action" data-documentid="{{ $document->id }}">
                                                <a class="sharedFileLink" href="/storage/{{ $document->filepath }}" target="_blank">{!! $document->icon() !!} <span>{{ $document->name }}</span></a> <button class="btn btn-sm btn-cancel deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
                                                <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6 bg-light">

                            <div class="card-header">
                                Upload Move-in Photos
                            </div>
                            <div class="card-body bg-light">
                                <div class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                    <div class="filesBox">
                                        <div class="h1 pb-1">
                                            <i class="fal fa-image"></i>
                                        </div>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="moveInImageUpload" multiple>
                                        <div class="custom-file-label btn btn-sm btn-primary" for="moveInImageUpload" data-browse=""><i class="fal fa-upload"></i> Upload Move-in Photos</div>
                                    </div>
                                    <small>
                                        Upload up to 15 photos. These files are not visible to your tenants.<br>
                                        Maximum size: 5Mb.<br>
                                        Allowed file types: jpg, png, gif.
                                    </small>
                                </div>
                                <div class="pt-3 @if($moveInPhotos->count() == 0) d-none @endif ">
                                    <div class="photoListBox sharedFileList" id="photoList">
                                        @foreach ($moveInPhotos as $document)
                                            <a href="/storage/{{ $document->filepath }}" data-toggle="modal" data-target="#imageModal" class="galleryItem" data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}" data-documentid="{{ $document->id }}">
                                                <div class="galleryItemContent" style="background-image: url(/storage/{{ $document->thumbnailpath ?? $document->filepath }})">
                                                    <div class="gallaryControl galleryTrash deleteDocument" data-documentid="{{ $document->id }}"><i class="fal fa-trash-alt text-white"></i></div>
                                                </div>
                                            </a>
                                            <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <button type="submit" name="back" value="back" class="btn btn-cancel btn-sm mr-3 woChecks">
                            <i class="fal fa-arrow-left mr-1"></i>Back
                        </button>
                        <button type="button" name="cancel" class="btn btn-cancel btn-sm mr-3" data-toggle="modal" data-target="#confirmCancelModal">
                            <i class="fal fa-times mr-1"></i>Cancel
                        </button>
                        <span class="btn-toolbar mb-2 mb-md-0 float-right">
                            <button type="submit" class="btn btn-primary btn-sm float-right btn-submit">
                                <div id="nextButtonTextGeneral">
                                    <i class="fal fa-check-circle mr-1"></i>
                                    Complete Move In
                                </div>
                                <div class="d-none" id="nextButtonTextDwolla">
                                    <i class="fal fa-check-circle mr-1"></i>
                                    @if(empty(Auth::user()->dwolla_tos))
                                        Agree and Complete Move In
                                    @else
                                        Complete Move In
                                    @endif
                                </div>
                            </button>
                        </span>
                    </div>
                </div><!-- /propertyForm -->
                @include('leases.add.cancel-modal-partial')
            </form>

        </div>
    </div>

    <div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <div class="modal-title pt-1"><strong>Move-in photo.</strong> Date/Time Uploaded: <span class="timeUploaded"></span></div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-1">
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function() {
            if ($("#financeAccount").val() === "_new"){
                $("#addFinanceAccountContent").collapse('show');
            } else {
                $("#addFinanceAccountContent").collapse('hide');
            }

            $("#financeAccount").change(function(){
               var val = $(this).val();
               if (val === "_new"){
                $("#addFinanceAccountContent").collapse('show');
               } else {
                   $("#addFinanceAccountContent").collapse('hide');
               }
            });

        });
    </script>
    <script>
        jQuery(document).ready(function() {
            $('#documentUpload').on('change', function () {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("unit_id", '{{ $data['unit_id'] }}');
                var ins = document.getElementById('documentUpload').files.length;
                var sizes_ok = true;
                var num_uploaded = 0;
                for (var x = 0; x < ins; x++) {
                    if(document.getElementById('documentUpload').files[x].size > 2000000){
                        sizes_ok = false;
                    } else {
                        form_data.append("documents[]", document.getElementById('documentUpload').files[x]);
                        num_uploaded++;
                    }
                }
                if((sizes_ok === false) && (num_uploaded === 0)){
                    alert("File is too big");
                    return;
                }

                var loadingbox =
                    '<li class="loadingBox list-group-item text-center list-group-item-action list-group-item-info bg-white">' +
                    '<img src="/images/loading.gif" style="margin:auto" />' +
                    '</li>';
                $('#sharedFileList').append(loadingbox);
                $('#sharedFileList').parent().removeClass('d-none');

                $.ajax({
                    url: '{{ route('leases/document-upload') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $('.loadingBox').remove();
                        var index;
                        var docbox = '';
                        for (index = 0; index < response.uploaded.length; ++index) {
                            if(response.uploaded[index].error){
                                docbox = docbox +
                                    '<li class="list-group-item list-group-item-action fileWithError list-group-item-danger">' +
                                    '<a class="sharedFileLink text-danger" href="javascript:void(0)">' + response.uploaded[index].icon +
                                    '<span>' + response.uploaded[index].name + '</span></a>' +
                                    '<strong class="float-right text-danger">' + response.uploaded[index].error + '</strong>' +
                                    '</li>';
                            } else {
                                docbox = docbox +
                                    '<li class="list-group-item list-group-item-action" data-documentid="' + response.uploaded[index].id + '">' +
                                    '<a class="sharedFileLink" href="' + response.uploaded[index].url + '" target="_blank">' + response.uploaded[index].icon +
                                    '<span>' + response.uploaded[index].name + '</span></a>' +
                                    '<button class="btn btn-sm btn-cancel deleteDocument" data-documentid="' + response.uploaded[index].id + '"><i class="fal fa-trash-alt mr-1"></i> Delete</button>' +
                                    '<input type="hidden" name="document_ids[]" value="' + response.uploaded[index].id + '">' +
                                    '</li>';
                            }
                        }
                        $('#sharedFileList').append(docbox);
                        window.setTimeout('$(".fileWithError").fadeOut("fast")', 3000);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            $('#moveInImageUpload').on('change', function () {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("unit_id", '{{ $data['unit_id'] }}');
                form_data.append("document_type", 'move_in_photo');
                var ins = document.getElementById('moveInImageUpload').files.length;
                var sizes_ok = true;
                var num_uploaded = 0;
                for (var x = 0; x < ins; x++) {
                    if(document.getElementById('moveInImageUpload').files[x].size > 5242880){
                        sizes_ok = false;
                    } else {
                        form_data.append("documents[]", document.getElementById('moveInImageUpload').files[x]);
                        num_uploaded++;
                    }
                }
                if((sizes_ok === false) && (num_uploaded === 0)){
                    alert("File is too big");
                    return;
                }

                var loadingbox =
                    '<div class="loadingBox galleryItem">' +
                        '<div class="galleryItemContent" style="background: url(/images/loading.gif) no-repeat center #fff">' +
                        '</div>' +
                    '</div>';
                $('#photoList').append(loadingbox);
                $('#photoList').parent().removeClass('d-none');

                $.ajax({
                    url: '{{ route('leases/move-in-out-upload') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $('.loadingBox').remove();
                        var index;
                        var docbox = '';
                        for (index = 0; index < response.uploaded.length; ++index) {
                            if(response.uploaded[index].error){
                                docbox = docbox +
                                    '<div class="loadingBox galleryItem fileWithError">' +
                                        '<div class="galleryItemContent" style="background:#ffacb2">' +
                                            '<div class="p-2 text-center text-danger">' + response.uploaded[index].error + '</div>' +
                                        '</div>' +
                                    '</div>';
                            } else {
                                docbox = docbox +
                                    '<a href="' + response.uploaded[index].url + '" class="galleryItem" data-toggle="modal" data-target="#imageModal" data-time_uploaded="' + response.uploaded[index].created_at + '"  data-documentid="' + response.uploaded[index].id + '">' +
                                        '<div class="galleryItemContent" style="background-image: url(' + response.uploaded[index].thumb_url + ')">' +
                                            '<div class="gallaryControl galleryTrash deleteDocument" data-documentid="' + response.uploaded[index].id + '"><i class="fal fa-trash-alt text-white"></i></div>' +
                                        '</div>' +
                                    '</a>' +
                                    '<input type="hidden" name="document_ids[]" value="' + response.uploaded[index].id + '">';
                            }
                        }
                        $('#photoList').append(docbox);
                        window.setTimeout('$(".fileWithError").fadeOut("fast")', 3000);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            $(document).on('click', '.deleteDocument', function(event){
                event.stopPropagation();
                event.preventDefault();
                var documentid = $(this).data('documentid');
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("document_id", documentid);
                $.ajax({
                    url: '{{ route('leases/document-delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $('.sharedFileList').find('li[data-documentid=' + response.document_id + ']').remove();
                        $('.sharedFileList').find('a[data-documentid=' + response.document_id + ']').remove();
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

        });

        $('.financeSwitchContent input').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#imageModal').on('show.bs.modal', function (event) {
            var t = $(event.relatedTarget);
            $('#imageModal').find('.modal-body').html('<img src="' + t.attr('href') + '">');
            $('#imageModal').find('.timeUploaded').html(t.data('time_uploaded'));
        });

        jQuery(document).ready(function() {
            if ($('#financeSwitchContentDwolla .is-invalid, #financeSwitchContentDwolla .customFormAlert').length > 0) {
                $('#financeSwitchContent1').hide();
                $('#financeSwitchContentDwolla').show();
                $('input[type="radio"][value="dwolla_target"]').prop('checked',true);
            }
            $('input[type=radio][name=financeSwitch]').change(function () {
                if (this.value == 'stripe') {
                    $('#financeSwitchContent1').show();
                    $('#financeSwitchContentDwolla').hide();
                    $('#nextButtonTextDwolla').addClass("d-none");
                    $('#nextButtonTextGeneral').removeClass("d-none");
                } else if (this.value == 'dwolla_target') {
                    $('#financeSwitchContent1').hide();
                    $('#financeSwitchContentDwolla').show();
                    $('#nextButtonTextDwolla').removeClass("d-none");
                    $('#nextButtonTextGeneral').addClass("d-none");
                }
            });
        });
    </script>
@endsection
