@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('dashboard') }}">Dashboard</a> > <a href="{{ route('profile/photo') }}">Update Photo</a>
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
                <!--<div class="filterToolbar btn-toolbar mb-2 mb-md-0">
                    <a href="#" class="btn btn-cancel btn-sm mr-3"><i class="fal fa-times mr-1"></i> Cancel</a>
                    <a href="#" class="btn btn-primary btn-sm"><i class="fal fa-check-circle mr-1"></i> Save</a>
                </div>-->
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">

                @include('includes.user.account-menu',['active' => 'photo'])

                <div class="profileNavTabsLeftContent col-md-9">
                    <form>
                        <div class="row">
                            <div class="navTabsLeftPhoto col-md-6 col-lg-5 col-xl-4">
                                <div class="card profileFormPhoto">
                                    <div class="card-header">
                                        <span>Profile photo</span>
                                        <a href="#" id="removePhoto" class="removeUnitButton float-right">remove <i class="fal fa-times"></i></a>
                                    </div>
                                    <div class="profileCardImageBox" id="profilePhotoPlaceholder">
                                        @if ($user->photoUrl())
                                            <img src="{{ $user->photoUrl() }}" alt="{{ Auth::user()->name }} {{ Auth::user()->lastname }}">
                                        @endif
                                    </div>
                                    <div class="@if ($user->photoUrl()) d-none @endif card-body text-center">
                                        <div class="display-2 pt-4 pb-4">
                                            <i class="fal fa-user-alt text-secondary"></i>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted text-center">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="profilePhoto">
                                            <div class="custom-file-label btn btn-sm btn-primary" for="profilePhoto" data-browse="">
                                                <i class="fal fa-upload"></i> <span>Upload photo</span>
                                            </div>
                                        </div>
                                        <small>Recommended size: 400x400 pixels, 5mb maximum</small>
                                    </div>
                                </div><!-- /propertyFormPhoto -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#profilePhoto').on('change', function () {
                if(document.getElementById('profilePhoto').files[0].size > 5242880){
                    alert("File is too big");
                    this.value = "";
                    return;
                }
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("profile_photo", document.getElementById('profilePhoto').files[0]);
                $('#profilePhotoPlaceholder').addClass('loading');
                $('.profileFormPhoto').find('.card-body').addClass('d-none');
                $.ajax({
                    url: '{{ route('files/profile/upload') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        console.log(response);
                        var url = response.uploaded[0].url;
                        $('#profilePhotoPlaceholder').removeClass('loading').html(
                            '<img src="' + url + '" class="card-img-top" alt="..." />'
                        );
                    },
                    error: function (response) {
                        console.log(response);
                        $('.profileFormPhoto').find('.card-body').removeClass('d-none');
                        $('#profilePhotoPlaceholder').removeClass('loading');
                    }
                });
            });

            $('#removePhoto').on('click', function(evnt){
                event.preventDefault();
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                $.ajax({
                    url: '{{ route('files/profile/delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        console.log('success response');
                        $('#profilePhotoPlaceholder').html('');
                        $('.profileFormPhoto').find('.card-body').removeClass('d-none');
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });

            });

        });
    </script>
@endsection
