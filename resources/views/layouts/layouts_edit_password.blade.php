
<form method="POST" action="{{ $route }}" class="needs-validation" novalidate>
     @csrf
{{--     @if ($errors->any())--}}
{{--         <div class="alert alert-danger">--}}
{{--             <ul>--}}
{{--                 @foreach ($errors->all() as $error)--}}
{{--                     <li>{{ $error }}</li>--}}
{{--                 @endforeach--}}
{{--             </ul>--}}
{{--         </div>--}}
{{--     @endif--}}
        <div class="card propertyForm propertyFormGeneralInfo">
            <div class="card-body bg-light">
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="currentPassword">Type Your Current Password Here<i class="required fal fa-asterisk"></i></label>
                        <div class="input-group showHidePassword">
                            <input name="current_password" class="form-control" type="password" id="currentPassword" >

                            <div class="input-group-append">
                                <button class="btn btn-grey" type="button"><i class="fal fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        @if ($errors->has('current_password'))
                            <div class="input-group" style="padding-top: 10px">
                            <span class="help-block">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="input-group" style="padding-top: 10px">
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="password">New Password <i class="required fal fa-asterisk"></i></label>
                        <div class="input-group showHidePassword">
                            <input name="password" type="password" class="form-control" id="password" required>

                            <div class="input-group-append">
                                <button class="btn btn-grey" type="button"><i class="fal fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        @if ($errors->has('password'))
                            <div class="input-group" style="padding-top: 10px">
                                <div class="alert alert-danger">
                                    {{ $errors->first('password')  }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirmPassword">Confirm Password <i class="required fal fa-asterisk"></i></label>
                        <div class="input-group showHidePassword">
                            <input name="password_confirmation" type="password" class="form-control" id="password_confirmation" required>
                            <div class="input-group-append">
                                <button class="btn btn-grey" type="button"><i class="fal fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        @if ($errors->has('password_confirmation'))
                            <div class="input-group" style="padding-top: 10px">
                                <div class="alert alert-danger">
                                    {{ $errors->first('password_confirmation')  }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="inRowComment">
                    <i class="fal fa-info-circle"></i>Please use password at least 8 characters long, with letters and numbers.
                </div>
            </div>
            <div class="card-footer text-muted">
                <a href="#" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                <button href="#" class="btn btn-primary btn-sm float-right"><i class="fal fa-check-circle mr-1"></i> Save</button>
            </div>
        </div><!-- /propertyForm -->
    </form>



