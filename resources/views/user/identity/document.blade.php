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
                <h4 class="alert-heading">Please upload document photo</h4>
                @if($identity->account_type == 'personal')
                    <p>Acceptable documents: Color copy of a valid government-issued photo ID (e.g., a driver’s license,
                        passport, or state ID card).</p>
                @endif
                @if($identity->account_type == 'soleProprietorship')
                    <p><strong>Sole Proprietorships</strong> can be verified by uploading Business documents as well as
                        Personal IDs.</p>
                    <p>Acceptable Business documents:</p>
                    <ul>
                        <li>Fictitious Business Name Statement</li>
                        <li>Certificate of Assumed Name; Business License,</li>
                        <li>Sales/Use Tax License,</li>
                        <li>Registration of Trade Name,</li>
                        <li>EIN documentation (IRS-issued SS4 confirmation letter),</li>
                    </ul>
                    <p>Acceptable Personal documents: Color copy of a valid government-issued photo ID (e.g., a driver’s
                        license, passport, or state ID card).</p>
                    <p><strong>Trusts</strong> will require signed trust documents that include the trust and username
                        that are on file.</p>
                @endif
                @if($identity->account_type == 'corporation' || $identity->account_type == 'llc' || $identity->account_type == 'partnership')
                    @if($identity->document_required == 'Controller')
                        <p>Controller verification required.</p>
                        <p>Acceptable documents: Color copy of a valid government-issued photo ID (e.g., a driver’s
                            license, passport, or state ID card).</p>
                    @endif
                    @if($identity->document_required == 'Business')
                        <p>Business verification required.</p>
                        <p>Business Identifying document we recommend uploading can include the following: EIN Letter
                            (IRS-issued SS4 confirmation letter).</p>
                    @endif
                    @if($identity->document_required == 'Controller and Business')
                        <!-- TODO UPLOAD 2 DOCUMENTS -->
                        <p>Controller verification required.</p>
                        <p>Acceptable documents: Color copy of a valid government-issued photo ID (e.g., a driver’s
                            license, passport, or state ID card).</p>
                    @endif
                @endif

                <p>Please meet the following requirements:</p>
                <ul>
                    <li>All 4 Edges of the document should be visible</li>
                    <li>A dark/high contrast background should be used</li>
                    <li>At least 90% of the image should be the document</li>
                    <li>Should be at least 300dpi</li>
                    <li>Capture image from directly above the document</li>
                    <li>Make sure that the image is properly aligned, not rotated, tilted or skewed</li>
                    <li>No flash to reduce glare</li>
                    <li>No black and white documents</li>
                    <li>No expired IDs</li>
                </ul>
                <hr>
                <p class="mb-0">Files must be no larger than 10MB in size.</p>
            </div>

        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">

                <div class="identityVerificationBar col-md-3"></div>

                <div class="profileNavTabsLeftContent col-md-9">

                    <div class="card propertyForm propertyFormGeneralInfo">

                        <form action="{{ route('profile/identity/document') }}" method="POST" id="identityForm"
                              class="needs-validation" novalidate>
                            @csrf

                            <div class="card-body bg-light identityBarCell">
                                <div class="identityBarItem identityBarItemTop active">
                                    <div class="identityBarTitle">Upload Document</div>
                                    <div class="identityBarIcon"><i class="fal fa-upload"></i></div>
                                </div>

                                <div class="pt-2">
                                    <div id="documentLoadBox" class="mb-3">
                                        <div class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                            <div class="filesBox">
                                                <div class="h1 pb-1">
                                                    <i class="fal fa-file-alt"></i>
                                                </div>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="documentUpload">
                                                <div class="custom-file-label btn btn-sm btn-primary"
                                                     for="documentUpload" data-browse=""><i
                                                        class="fal fa-mouse-pointer"></i> Select Document to Upload
                                                </div>
                                            </div>
                                            <small>
                                                Maximum size: 10Mb. Allowed file types: jpg, jpeg, png
                                            </small>
                                        </div>
                                    </div>

                                    <div class="">
                                        <ul id="sharedFileList" class="sharedFileList list-group">
                                            @foreach ($identity->userIdentityDocuments as $document)
                                                <li class="list-group-item list-group-item-action"
                                                    data-documentid="{{ $document->id }}">
                                                    {{--}}<a class="sharedFileLink" href="/storage/{{ $document->filepath }}" data-status="{{ $document->status }}" data-toggle="modal" data-target="#imageModal" class="galleryItem" data-photo_type="Identity Photo." data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}" ><i class="fal fa-file-image"></i> <span>{{ $document->name }}</span></a>{{--}}
                                                    <a class="sharedFileLink"
                                                       href="{{ route('profile/identity/view-document',['id_hash' => md5($document->id) . "." . $document->extension, 'nocache' => rand(1,999999)]) }}"
                                                       data-status="{{ $document->status }}" data-toggle="modal"
                                                       data-target="#imageModal" class="galleryItem"
                                                       data-photo_type="Identity Photo."
                                                       data-time_uploaded="{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y, g:i a') }}"><i
                                                            class="fal fa-file-image"></i>
                                                        <span>{{ $document->name }}</span></a>
                                                    @if($document->status == 'approved')
                                                        <div>
                                                            <span class="badge badge-success">Approved</span>
                                                        </div>
                                                    @endif
                                                    @if($document->status == 'failed')
                                                        <div>
                                                            <span class="badge badge-danger">Failed</span>
                                                            <span
                                                                class="text-danger ml-1"><small>Reason: {{ $document->failure_description }}</small></span>
                                                        </div>
                                                    @endif
                                                    @if($document->status == 'ready')
                                                        <button class="btn btn-sm btn-cancel deleteDocument"
                                                                data-documentid="{{ $document->id }}"><i
                                                                class="fal fa-trash-alt mr-1"></i> Delete
                                                        </button>
                                                        <input type="hidden" name="document_ids[]"
                                                               value="{{ $document->id }}">
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-4 mt-3">
                                            <label for="document_type">Document Type <i
                                                    class="required fal fa-asterisk"></i></label>
                                            <select class="form-control" name="document_type" id="document_type"
                                                    value="{{ old('document_type') }}">
                                                @if($identity->account_type == 'personal')
                                                    <option value="passport">Passport</option>
                                                    <option value="license">Driver's License</option>
                                                    <option value="idCard">Other U.S. government-issued photo id card
                                                    </option>
                                                @elseif($identity->account_type == 'soleProprietorship')
                                                    <option value="passport">Passport</option>
                                                    <option value="license">Driver's License</option>
                                                    <option value="idCard">Other U.S. government-issued photo id card
                                                    </option>
                                                    <option value="other">Business document</option>
                                                @elseif($identity->account_type == 'soleProprietorship')
                                                @else
                                                    <option value="passport">Passport</option>
                                                    <option value="license">Driver's License</option>
                                                    <option value="idCard">Other U.S. government-issued photo id card
                                                    </option>
                                                @endif
                                            </select>
                                            <div class="inRowComment pt-1">
                                                <i class="fal fa-info-circle"></i> Military IDs are not accepted
                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                            <input type="hidden" id="send_for_verification" name="send_for_verification" value="0">
                            <input type="hidden" name="identity_id" value="{{ $identity->id }}">
                        </form>

                        <div class="card-footer text-muted d-flex justify-content-between identityBarCell">
                            <div class="identityBarItem identityBarItemBottom d-none d-md-flex">
                                <div class="identityBarTitle">Submit for Review</div>
                                <div class="identityBarIcon"><i class="fal fa-paper-plane"></i></div>
                            </div>

                            <div></div>
                            <div>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        id="submitForVerification">
                                    <i class="fal fa-shield-alt mr-1"></i> Submit For Verification
                                </button>
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
                                    <select class="form-control" name="account_type" id="dwollaAccountTypeSwitch"
                                            disabled>
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
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                               id="city" name="city" value="{{ $identity->city ?? '' }}" maxlength="64"
                                               readonly>
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
                                        <input name="zip" maxlength="5" type="text" class="form-control" id="zip"
                                               placeholder="" value="{{ $identity->zip ?? '' }}" maxlength="32"
                                               readonly>
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
                                        <input type="text"
                                               class="form-control @error('first_name') is-invalid @enderror"
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
                                               value="{{ old('email') ?? $identity->email ?? $user->email }}"
                                               maxlength="64" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label for="dob">
                                            DOB
                                        </label>
                                        <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                               id="dob" name="dob"
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
                                        <input type="text"
                                               class="form-control @error('business_name') is-invalid @enderror"
                                               name="business_name" id="business_name"
                                               value="{{ old('business_name') ?? $identity->business_name ?? '' }}"
                                               readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="business_classification">Business Classification</label>
                                        <select class="form-control" name="business_classification"
                                                id="business_classification" disabled>
                                            @foreach($classifications as $class1)
                                                <optgroup label="{{ $class1->name }}">
                                                    @foreach($class1->_embedded->{'industry-classifications'} as $c)
                                                        @if($identity->business_classification == $c->id)
                                                            <option value="{{ $c->id }}"
                                                                    selected>{{ $c->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ein">Employer Identification Number</label>
                                        <input type="text" class="form-control @error('ein') is-invalid @enderror"
                                               name="ein" id="ein" value="{{ old('ein') ?? $identity->ein ?? ''}}"
                                               readonly>
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
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                               id="city" name="city" value="{{ old('city') ?? $identity->city ?? '' }}"
                                               readonly>
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
                                               class="form-control @error('zip') is-invalid @enderror" id="zip"
                                               placeholder="" value="{{ old('zip') ?? $identity->zip ?? '' }}" readonly>
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
                                        <select
                                            class="form-control @error('business_classification') is-invalid @enderror"
                                            name="business_classification" id="business_classification">
                                            @foreach($classifications as $class1)
                                                <optgroup label="{{ $class1->name }}">
                                                    @foreach($class1->_embedded->{'industry-classifications'} as $c)
                                                        @if($identity->business_classification == $c->id)
                                                            <option value="{{ $c->id }}"
                                                                    selected>{{ $c->name }}</option>
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
                                        <input name="zip" maxlength="5" type="text" class="form-control" id="zip"
                                               placeholder="" value="{{ $identity->zip ?? '' }}" readonly>
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
                                               id="controller_first_name"
                                               value="{{ $identity->controller_first_name ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="controller_last_name">Last Name</label>
                                        <input type="text" class="form-control" name="controller_last_name"
                                               id="controller_last_name"
                                               value="{{ $identity->controller_last_name ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="controller_title">Title</label>
                                        <input type="text" class="form-control" name="controller_title"
                                               id="controller_title" value="{{ $identity->controller_title ?? '' }}"
                                               readonly>
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
                                               id="controller_address_2"
                                               value="{{ $identity->controller_address_2 ?? '' }}" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="controller_city">City</label>
                                        <input type="text" class="form-control" id="controller_city"
                                               name="controller_city" value="{{ $identity->controller_city ?? '' }}"
                                               readonly>
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
                                               id="controller_zip" placeholder=""
                                               value="{{ $identity->controller_zip ?? '' }}" readonly>
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
        </div>
    </div>

    <div class="modal fade" id="confirmVerificationModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmVerificationModalTitle" aria-hidden="true">
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
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        $(document).ready(function () {
            checkReadyToSubmit();

            $('#documentUpload').on('change', function () {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("user_identity_id", '{{ $identity->id }}');
                var ins = document.getElementById('documentUpload').files.length;
                var sizes_ok = true;
                var num_uploaded = 0;
                for (var x = 0; x < ins; x++) {
                    if (document.getElementById('documentUpload').files[x].size > 10000000) {
                        sizes_ok = false;
                    } else {
                        form_data.append("documents[]", document.getElementById('documentUpload').files[x]);
                        num_uploaded++;
                    }
                }
                if ((sizes_ok === false) && (num_uploaded === 0)) {
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
                    url: '{{ route('profile/identity/upload') }}',
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
                            if (response.uploaded[index].error) {
                                docbox = docbox +
                                    '<li class="list-group-item list-group-item-action fileWithError list-group-item-danger">' +
                                    '<a class="sharedFileLink text-danger" href="javascript:void(0)"><i class="fal fa-file-image"></i>' +
                                    '<span>' + response.uploaded[index].name + '</span></a>' +
                                    '<strong class="float-right text-danger">' + response.uploaded[index].error + '</strong>' +
                                    '</li>';
                            } else {
                                docbox = docbox +
                                    '<li class="list-group-item list-group-item-action" data-documentid="' + response.uploaded[index].id + '">' +
                                    '<a class="sharedFileLink" href="' + response.uploaded[index].url + '" data-status="ready" data-toggle="modal" data-target="#imageModal" data-time_uploaded="' + response.uploaded[index].created_at + '" ><i class="fal fa-file-image"></i>' +
                                    '<span>' + response.uploaded[index].name + '</span></a>' +
                                    '<button class="btn btn-sm btn-cancel deleteDocument" data-documentid="' + response.uploaded[index].id + '"><i class="fal fa-trash-alt mr-1"></i> Delete</button>' +
                                    '<input type="hidden" name="document_ids[]" value="' + response.uploaded[index].id + '">' +
                                    '</li>';
                            }
                        }
                        $('#sharedFileList').append(docbox);
                        checkReadyToSubmit();
                        window.setTimeout('$(".fileWithError").fadeOut("fast")', 3000);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            $(document).on('click', '.deleteDocument', function (event) {
                event.stopPropagation();
                event.preventDefault();
                var documentid = $(this).data('documentid');
                var document_name = $(this).parent("li").find("span").text();
                $("#confirmDeleteDocument").data('documentid', documentid);
                $("#modalFileName").text(document_name);
                $('#confirmFileDeleteModal').modal();
            });

            $(document).on('click', '#confirmDeleteDocument', function (event) {
                event.preventDefault();
                var documentid = $(this).data('documentid');
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("document_id", documentid);
                $(".preloader").fadeIn("slow");
                $.ajax({
                    url: '{{ route('profile/identity/delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $(".preloader").fadeOut("fast");
                        $('.sharedFileList').find('li[data-documentid=' + response.document_id + ']').remove();
                        $('.sharedFileList').find('a[data-documentid=' + response.document_id + ']').remove();
                        $('#confirmFileDeleteModal').modal('hide');
                        checkReadyToSubmit();
                    },
                    error: function (response) {
                        $('#confirmFileDeleteModal').modal('hide');
                        console.log(response);
                    }
                });
            });

            $("#submitForVerification").on('click', function (event) {
                event.stopPropagation();
                event.preventDefault();

                var hasReady = $("#sharedFileList").find('[data-status="ready"]').length > 0;

                if (hasReady) {
                    $("#documentLoadBox").find(".card-body").removeClass("errorUploadRequired");
                    $('#confirmVerificationModal').modal();
                } else {
                    $("#documentLoadBox").find(".card-body").addClass("errorUploadRequired");
                }
            });

            $('#sendForVerificationButton').click(function () {
                $('#send_for_verification').val('yes');
                $('#confirmVerificationModal').modal('hide');
                $(".preloader").fadeIn("fast");
                $('#identityForm').submit();
            });

            $('#imageModal').on('show.bs.modal', function (event) {
                var t = $(event.relatedTarget);
                $('#imageModal').find('.modal-body').html('<img src="' + t.attr('href') + '">');
                $('#imageModal').find('.timeUploaded').html(t.data('time_uploaded'));
                $('#imageModal').find('.photoType').html(t.data('photo_type'));
            });

        });

        function checkReadyToSubmit() {
            var hasReady = $("#sharedFileList").find('[data-status="ready"]').length > 0;
            if (hasReady) {
                $("#documentLoadBox").hide();
            } else {
                $("#documentLoadBox").show();
            }
        }
    </script>
@endsection
