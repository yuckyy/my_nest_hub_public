@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/identity') }}">User
            Verification</a>
    </div>

    <div class="container-fluid pb-4">

        <div class="container-fluid">
            <h1 class="h2 pt-4 pb-2">Identity Verification</h1>
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <div class="alert alert-primary" role="alert">
                <h4 class="alert-heading">Your document has been sent</h4>
                <p class="m-0">We are reviewing your document. This process may take anywhere from a few seconds up to
                    1-2 business days.</p>
            </div>
        </div>

        <div class="container-fluid unitFormContainer">

            <div class="card propertyForm propertyFormGeneralInfo">
                <div class="card-header">
                    <span>Uploaded Documents</span>
                </div>
                <div class="card-body bg-light">
                    <div class="pt-2">
                        <div class="">
                            <ul id="sharedFileList" class="sharedFileList list-group">
                                @foreach ($identity->userIdentityDocuments as $document)
                                    <li class="list-group-item list-group-item-action"
                                        data-documentid="{{ $document->id }}">
                                        {{--}}<a class="sharedFileLink galleryItem" href="/storage/{{ $document->filepath }}" data-status="{{ $document->status }}" data-toggle="modal" data-target="#imageModal" data-photo_type="Identity Photo." data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}" ><i class="fal fa-file-image"></i> <span>{{ $document->name }}</span></a>{{--}}
                                        <a class="sharedFileLink"
                                           href="{{ route('profile/identity/view-document',['id_hash' => md5($document->id) . "." . $document->extension, 'nocache' => rand(1,999999)]) }}"
                                           data-status="{{ $document->status }}" data-toggle="modal"
                                           data-target="#imageModal" class="galleryItem"
                                           data-photo_type="Identity Photo."
                                           data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}"><i
                                                class="fal fa-file-image"></i> <span>{{ $document->name }}</span></a>
                                        @if($document->status == 'approved')
                                            <div>
                                                <span class="badge badge-success">Approved</span>
                                            </div>
                                        @endif
                                        @if($document->status == 'review')
                                            <div>
                                                <span class="badge badge-light">Under Verification</span>
                                            </div>
                                        @endif
                                        @if($document->status == 'failed')
                                            <div>
                                                <span class="badge badge-danger">Failed</span>
                                                <span
                                                    class="text-danger ml-1"><small>Reason: {{ $document->failure_description }}</small></span>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 propertyForm propertyFormGeneralInfo">
                <div class="card-header">
                    <span>Saved User Information</span>
                </div>
                <div class="card-body bg-light">
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="dwollaAccountTypeSwitch">User Profile Type </label>
                            <select class="form-control" name="account_type" id="dwollaAccountTypeSwitch" disabled>
                                @php
                                    $accountTypes = App\Models\UserIdentity::getAccountTypes();
                                @endphp
                                @foreach($accountTypes as $key => $name)
                                    @if($identity->account_type == $key)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @if($identity->account_type == 'personal')
                    <div class="card-body bg-light border-top">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name">First Name </label>
                                <input type="text" class="form-control" name="first_name" id="first_name"
                                       value="{{ $identity->first_name }}" maxlength="64" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name">Last Name </label>
                                <input type="text" class="form-control" name="last_name" id="last_name"
                                       value="{{ $identity->last_name }}" maxlength="64" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email">Email </label>
                                <input type="text" class="form-control" name="email" id="email"
                                       value="{{ $identity->email }}" maxlength="64" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="address">Address </label>
                                <input type="text" class="form-control" name="address" id="address"
                                       value="{{ $identity->address }}" maxlength="255" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_2">Address Second Line</label>
                                <input type="text" class="form-control" name="address_2" id="address_2"
                                       value="{{ $identity->address_2 }}" maxlength="255" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="city">City </label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                       name="city" value="{{ $identity->city ?? '' }}" maxlength="64" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state">State </label>
                                @foreach ($states as $s)
                                    @if(($identity->state ?? '' ) == $s->code )
                                        <input name="state" class="form-control" id="state" readonly
                                               value="{{ $s->name }}">
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zip">Zip Code </label>
                                <input name="zip" maxlength="5" type="text" class="form-control" id="zip" placeholder=""
                                       value="{{ $identity->zip ?? '' }}" maxlength="32" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="dob">
                                    DOB
                                </label>
                                <input type="date" class="form-control" id="dob" name="dob"
                                       value="{{ (($identity && $identity->dob) ? \Carbon\Carbon::parse($identity->dob)->format("Y-m-d") : \Carbon\Carbon::now()->subYears(18)->format("Y-m-d")) }}"
                                       required="required" readonly>
                            </div>
                        </div>
                    </div>
                @endif
                @if($identity->account_type == 'soleProprietorship')

                    <div class="card-header border-top">
                        <span>Business Owner</span>
                    </div>
                    <div class="card-body bg-light">

                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       name="first_name" id="first_name"
                                       value="{{ old('first_name') ?? $identity->first_name ?? $user->name }}"
                                       maxlength="64" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       name="last_name" id="last_name"
                                       value="{{ old('last_name') ?? $identity->last_name ?? $user->lastname }}"
                                       maxlength="64" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email">Email</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror"
                                       name="email" id="email"
                                       value="{{ old('email') ?? $identity->email ?? $user->email }}" maxlength="64"
                                       readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="dob">
                                    DOB
                                </label>
                                <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob"
                                       name="dob"
                                       value="{{ old('dob') ?? (($identity && $identity->dob) ? \Carbon\Carbon::parse($identity->dob)->format("Y-m-d") : \Carbon\Carbon::now()->subYears(18)->format("Y-m-d")) }}"
                                       readonly>
                            </div>
                        </div>

                    </div>
                    <div class="card-header border-top">
                        <span>Business</span>
                    </div>
                    <div class="card-body bg-light">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="business_name">Business Name</label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror"
                                       name="business_name" id="business_name"
                                       value="{{ old('business_name') ?? $identity->business_name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="business_classification">Business Classification</label>
                                <select class="form-control" name="business_classification" id="business_classification"
                                        disabled>
                                    @foreach($classifications as $class1)
                                        <optgroup label="{{ $class1->name }}">
                                            @foreach($class1->_embedded->{'industry-classifications'} as $c)
                                                @if($identity->business_classification == $c->id)
                                                    <option value="{{ $c->id }}" selected>{{ $c->name }}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ein">Employer Identification Number</label>
                                <input type="text" class="form-control @error('ein') is-invalid @enderror" name="ein"
                                       id="ein" value="{{ old('ein') ?? $identity->ein ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="address">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                       name="address" id="address"
                                       value="{{ old('address') ?? $identity->address ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_2">Address Second Line</label>
                                <input type="text" class="form-control @error('address_2') is-invalid @enderror"
                                       name="address_2" id="address_2"
                                       value="{{ old('address_2') ?? $identity->address_2 ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="city">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                       name="city" value="{{ old('city') ?? $identity->city ?? '' }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state">State</label>
                                @foreach ($states as $s)
                                    @if(($identity->state ?? '' ) == $s->code )
                                        <input name="state" class="form-control" id="state" readonly
                                               value="{{ $s->name }}">
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zip">Zip Code</label>
                                <input name="zip" maxlength="5" type="text"
                                       class="form-control @error('zip') is-invalid @enderror" id="zip" placeholder=""
                                       value="{{ old('zip') ?? $identity->zip ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>
                @endif
                @if(
                    $identity->account_type == 'corporation' ||
                    $identity->account_type == 'llc' ||
                    $identity->account_type == 'partnership'
                )
                    <div class="card-header border-top">
                        <span>MYNESTHUB Account Administrator</span>
                    </div>
                    <div class="card-body bg-light">
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" name="first_name" id="first_name"
                                       value="{{ $identity->first_name }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="last_name"
                                       value="{{ $identity->last_name }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" name="email" id="email"
                                       value="{{ $identity->email }}" maxlength="64" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="card-header border-top">
                        <span>Business</span>
                    </div>
                    <div class="card-body bg-light">

                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="business_name">Business Name</label>
                                <input type="text" class="form-control" name="business_name" id="business_name"
                                       value="{{ $identity->business_name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="business_classification">Business Classification</label>
                                <select class="form-control @error('business_classification') is-invalid @enderror"
                                        name="business_classification" id="business_classification">
                                    @foreach($classifications as $class1)
                                        <optgroup label="{{ $class1->name }}">
                                            @foreach($class1->_embedded->{'industry-classifications'} as $c)
                                                @if($identity->business_classification == $c->id)
                                                    <option value="{{ $c->id }}" selected>{{ $c->name }}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ein">Employer Identification Number</label>
                                <input type="text" class="form-control" name="ein" id="ein"
                                       value="{{ $identity->ein ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" name="address" id="address"
                                       value="{{ $identity->address ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_2">Address Second Line</label>
                                <input type="text" class="form-control" name="address_2" id="address_2"
                                       value="{{ $identity->address_2 ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                       value="{{ $identity->city ?? '' }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state">State</label>
                                @foreach ($states as $s)
                                    @if(($identity->state ?? '' ) == $s->code )
                                        <input name="state" class="form-control" id="state" readonly
                                               value="{{ $s->name }}">
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zip">Zip Code</label>
                                <input name="zip" maxlength="5" type="text" class="form-control" id="zip" placeholder=""
                                       value="{{ $identity->zip ?? '' }}" readonly>
                            </div>
                        </div>

                    </div>
                    <div class="card-header border-top">
                        <span>Controller</span>
                    </div>
                    <div class="card-body bg-light">

                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="controller_first_name">First Name</label>
                                <input type="text" class="form-control" name="controller_first_name"
                                       id="controller_first_name" value="{{ $identity->controller_first_name ?? '' }}"
                                       readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="controller_last_name">Last Name</label>
                                <input type="text" class="form-control" name="controller_last_name"
                                       id="controller_last_name" value="{{ $identity->controller_last_name ?? '' }}"
                                       readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="controller_title">Title</label>
                                <input type="text" class="form-control" name="controller_title" id="controller_title"
                                       value="{{ $identity->controller_title ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="controller_address">Address</label>
                                <input type="text" class="form-control" name="controller_address"
                                       id="controller_address" value="{{ $identity->controller_address ?? '' }}"
                                       readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="controller_address_2">Address Second Line</label>
                                <input type="text" class="form-control" name="controller_address_2"
                                       id="controller_address_2" value="{{ $identity->controller_address_2 ?? '' }}"
                                       readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="controller_city">City</label>
                                <input type="text" class="form-control" id="controller_city" name="controller_city"
                                       value="{{ $identity->controller_city ?? '' }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="controller_state">State</label>
                                @foreach ($states as $s)
                                    @if(($identity->controller_state ?? '' ) == $s->code )
                                        <input name="state" class="form-control" id="state" readonly
                                               value="{{ $s->name }}">
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="controller_zip">Zip Code</label>
                                <input name="controller_zip" maxlength="5" type="text" class="form-control"
                                       id="controller_zip" placeholder="" value="{{ $identity->controller_zip ?? '' }}"
                                       readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="dob">
                                    DOB
                                </label>
                                <input
                                    type="date"
                                    class="form-control @error('dob') is-invalid @enderror"
                                    id="dob"
                                    name="dob"
                                    value="{{ (($identity && $identity->dob) ? \Carbon\Carbon::parse($identity->dob)->format("Y-m-d") : \Carbon\Carbon::now()->subYears(18)->format("Y-m-d")) }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <div class="card mt-4">
                <div class="card-header text-danger mb-0">
                    <strong>Danger Zone</strong>
                </div>
                <div class="card-body">
                    <h3>Cancel Verification</h3>
                    <div class="inRowComment">
                        <i class="fal fa-info-circle"></i> This operation cannot be undone.</span>
                    </div>

                    <button type="button" class="btn btn-danger btn-sm mr-3 end-lease-btn" data-toggle="modal"
                            data-target="#confirmCancelVerificationModal">
                        <i class="fal fa-ban mr-1"></i> Cancel Verification
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="confirmCancelVerificationModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmCancelVerificationModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmCancelVerificationModalTitle">Confirm Cancel Verification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <p>Are you sure you want to cancel your user verification?</p>
                    <p>This operation cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fal fa-times mr-1"></i> Don't Change
                    </button>
                    <form action="{{ route('profile/identity/unverify') }}" method="POST">
                        @csrf
                        <input type="hidden" name="identity_id" value="{{ $identity->id }}">
                        <button class="btn btn-danger btn-sm mr-3" type="submit">
                            <i class="fal fa-ban mr-1"></i> Yes, Cancel Verification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE FILE confirmation dialog-->
    <div class="modal fade" id="confirmFileDeleteModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmFileDeleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmFileDeleteModalTitle">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div>Are you sure you would like to delete <strong id="modalFileName"></strong>?</div>
                    <div class="loadingHolder"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fal fa-times mr-1"></i>
                        Cancel
                    </button>
                    <button id="confirmDeleteDocument" class="btn btn-danger btn-sm mr-3" type="button">
                        <i class="fal fa-trash mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <div class="modal-title pt-1"><strong class="photoType">Identity Document.</strong> Date/Time
                        Uploaded: <span class="timeUploaded"></span></div>
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
    <script>
        $(document).ready(function () {
            $('#imageModal').on('show.bs.modal', function (event) {
                var t = $(event.relatedTarget);
                $('#imageModal').find('.modal-body').html('<img src="' + t.attr('href') + '">');
                $('#imageModal').find('.timeUploaded').html(t.data('time_uploaded'));
                $('#imageModal').find('.photoType').html(t.data('photo_type'));
            });
        });
    </script>
@endsection