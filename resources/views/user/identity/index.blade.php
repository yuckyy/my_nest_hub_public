@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/identity') }}">User Verification</a>
    </div>

    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <h1 class="h2 pt-4 pb-2">Identity Verification</h1>
            <div class="alert alert-primary" role="alert">
                <p class="m-0"><i class="fal fa-info-circle"></i> Please provide us an accurate information and submit for review. You will have two attempts.</p>
            </div>
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="identityVerificationBar col-md-3"></div>

                <div class="profileNavTabsLeftContent col-md-9">
                    <form method="POST" id="identityForm" class="needs-validation checkUnload" novalidate>
                        @csrf

                        <div class="card propertyForm propertyFormGeneralInfo">
                            {{--}}
                            <div class="card-header">
                                <span>User Verification</span>
                            </div>
                            {{--}}
                            <div class="card-body bg-light identityBarCell">
                                <div class="identityBarItem identityBarItemTop active">
                                    <div class="identityBarTitle">Account Type</div>
                                    <div class="identityBarIcon"><i class="fal fa-user-tie"></i></div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="dwollaAccountTypeSwitch">User Profile Type <i class="required fal fa-asterisk"></i></label>
                                        <select class="form-control" name="account_type" id="dwollaAccountTypeSwitch">
                                            @php
                                            $accountTypes = App\Models\UserIdentity::getAccountTypes();
                                            @endphp
                                            @foreach($accountTypes as $key => $name)
                                                <option value="{{ $key }}" @if($account_type == $key) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            {{--}}
                            <div class="pb-1">
                                <div class="inRowComment">
                                    <i class="fal fa-info-circle"></i> Please provide us an accurate information and submit for review. You will have two attempts.
                                </div>
                            </div>
                            {{--}}
                            @include($formView, ['user' => $user, 'identity' => $identity, 'classifications' => $classifications])

                            <div class="card-footer text-muted d-flex justify-content-between identityBarCell">
                                <div class="identityBarItem identityBarItemBottom d-none d-md-flex">
                                    <div class="identityBarTitle">Submit for Review</div>
                                    <div class="identityBarIcon"><i class="fal fa-paper-plane"></i></div>
                                </div>

                                <a href="{{ route("profile/finance") }}" class="btn btn-cancel btn-sm mr-3 d-none d-sm-inline-block">
                                    <i class="fal fa-times mr-1"></i> Cancel
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm mr-2">
                                        <i class="fal fa-check-circle mr-1"></i> Save
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#confirmVerificationModal">
                                        <i class="fal fa-shield-alt mr-1"></i> Save & Submit For Verification
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="send_for_verification" name="send_for_verification" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmVerificationModal" tabindex="-1" role="dialog" aria-labelledby="confirmVerificationModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmVerificationModalTitle">Confirm Send for verification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>Are you sure you want to send this info for verification?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> Cancel
                    </button>
                    <button id="sendForVerificationButton" class="btn btn-primary btn-sm" type="button">
                        <i class="fal fa-shield-alt mr-1"></i> Yes, Send
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function() {
            $('#sendForVerificationButton').click(function(){
                $('#send_for_verification').val('yes');
                $('#confirmVerificationModal').modal('hide');
                $(".preloader").fadeIn("fast");
                $('#identityForm').submit();
            });
            $('#dwollaAccountTypeSwitch').change(function(){
                var account_type = $(this).val();
                $(".preloader").fadeIn("fast");
                document.location.href = '{{ route('profile/identity') }}?account_type=' + account_type;
            });

            $(".identityBarCell input").on('focus', function(){
                $(this).closest('.identityBarCell').find(".identityBarItem").addClass('active');
            });
            @if(!empty(old('address')) || !empty($identity->address))
                $(".identityBarItem").not(".identityBarItemBottom").addClass('active');
            @endif
        });
    </script>
@endsection
