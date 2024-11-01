@extends('layouts.app')

@section('content')
    @include('includes.units.breadcrumbs')
    <div class="container-fluid pb-4">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4">
                @include('properties.units.header-partial')
            </div>
        </div>

        <div class="container-fluid unitFormContainer">
            <div class="row">
                <div class="navTabsLeftContainer col-md-3">
                    @include('includes.units.menu')
                </div>
                <div class="navTabsLeftContent col-md-9">
                    <div class="accordion" id="accordionShare">

                        <div class="card propertyForm">

                            <form action="{{ route('properties/units/share/terms-save',['unit' => $unit->id]) }}" class="needs-validation checkUnload" novalidate method="POST">
                                @csrf
                                <div class="accHeader collapsed card-header d-flex justify-content-between" id="headingDescription" data-toggle="collapse" data-target="#collapseDescription" >
                                    <div>
                                        <i class="fal fa-flag-checkered mr-1 h5 mb-0 d-inline-block align-middle"></i> <span class="d-inline-block align-middle">Terms</span>
                                    </div>
                                    <button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse" data-target="#collapseDescription" aria-expanded="true" aria-controls="collapseDescription" style="margin: -5px 0">
                                        <span>Show  <i class="fal fa-eye ml-1"></i></span>
                                        <span class="d-none">Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                                    </button>
                                </div>
                                <div id="collapseDescription" class="collapse marketingCollapseItem" aria-labelledby="headingDescription" data-parent="#accordionShare">
                                    <div class="card-body bg-light border-top">

                                        <div class="row">
                                            <div class="col-md mb-3">
                                                <label for="availableDateField">
                                                    Available Date
                                                </label>
                                                <input
                                                        type="date"
                                                        class="form-control @error('available_date') is-invalid @enderror"
                                                        id="availableDateField"
                                                        name="available_date"
                                                        value="{{
                                                            old('available_date')
                                                            ?? ($unit->available_date ? Carbon\Carbon::parse($unit->available_date)->format("Y-m-d") : null)
                                                        }}"
                                                >
                                                <span class="invalid-feedback" role="alert">
                                                    @error('available_date')
                                                    {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                            <div class="col-md mb-3">
                                                <label for="durationField">
                                                    Duration
                                                </label>
                                                <div class="input-group">
                                                    <input
                                                            type="number"
                                                            name="duration"
                                                            id="durationField"
                                                            class="form-control @error('duration') is-invalid @enderror"
                                                            value="{{ old('duration') ?? $unit->duration ?? "" }}"
                                                            min="0"
                                                    >
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Month</span>
                                                    </div>
                                                    <span class="invalid-feedback" role="alert">
                                                        @error('duration')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md mb-3">
                                                <label for="monthlyRentField">
                                                    Monthly Rent
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input
                                                            type="text"
                                                            class="form-control @error('monthly_rent') is-invalid @enderror"
                                                            name="monthly_rent"
                                                            value="{{ old('monthly_rent') ?? $unit->monthly_rent ?? ""  }}"
                                                            data-type="currency"
                                                            maxlength="12"
                                                            data-maxamount="9999999"
                                                            id="monthlyRentField"
                                                    >
                                                    <span class="invalid-feedback" role="alert">
                                                        @error('monthly_rent')
                                                        {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md mb-3">
                                                <label for="securityDepositField">
                                                    Security Deposit
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input
                                                            type="text"
                                                            class="form-control @error('security_deposit') is-invalid @enderror"
                                                            name="security_deposit"
                                                            value="{{ old('security_deposit') ?? $unit->security_deposit ?? ""  }}"
                                                            data-type="currency"
                                                            maxlength="12"
                                                            data-maxamount="9999999"
                                                            id="securityDepositField"
                                                    >
                                                    <span class="invalid-feedback" role="alert">
                                                        @error('security_deposit')
                                                        {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="unitDescriptionField">Description</label>
                                            <textarea name="description" id="unitDescriptionField" rows="3" maxlength="65000"
                                                      class="form-control">{{ $unit->description }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md mb-3">
                                                <label for="minimumCreditField">
                                                    Minimum Credit
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input
                                                            type="text"
                                                            class="form-control @error('minimum_credit') is-invalid @enderror"
                                                            name="minimum_credit"
                                                            value="{{ old('minimum_credit') ?? $unit->minimum_credit ?? ""  }}"
                                                            data-type="currency"
                                                            maxlength="12"
                                                            data-maxamount="9999999"
                                                            id="minimumCreditField"
                                                    >
                                                    <span class="invalid-feedback" role="alert">
                                                        @error('minimum_credit')
                                                        {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md mb-3">
                                                <label for="minimumIncomeField">
                                                    Minimum Income
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">$</div>
                                                    </div>
                                                    <input
                                                            type="text"
                                                            class="form-control @error('minimum_income') is-invalid @enderror"
                                                            name="minimum_income"
                                                            value="{{ old('minimum_income') ?? $unit->minimum_income ?? ""  }}"
                                                            data-type="currency"
                                                            maxlength="12"
                                                            data-maxamount="9999999"
                                                            id="minimumIncomeField"
                                                    >
                                                    <span class="invalid-feedback" role="alert">
                                                        @error('minimum_income')
                                                        {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="additionalRequirementsField">Additional Requirements</label>
                                            <textarea name="additional_requirements" id="additionalRequirementsField" rows="3" maxlength="65000"
                                                      class="form-control">{{ $unit->additional_requirements }}</textarea>
                                        </div>

                                    </div>
                                    <div class="card-footer bg-light text-right">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fal fa-check-circle mr-1"></i> Save Terms
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card propertyForm propertyAmenitiesForm">

                            <form action="{{ route('properties/units/share/amenities-save',['unit' => $unit->id]) }}" class="needs-validation checkUnload" novalidate method="POST">
                                @csrf
                                <div class="accHeader collapsed card-header d-flex justify-content-between" id="headingOne" data-toggle="collapse" data-target="#collapseOne" >
                                    <div>
                                        <i class="fal fa-clipboard-list-check mr-1 h5 mb-0 d-inline-block align-middle"></i> <span class="d-inline-block align-middle">Amenities</span>
                                    </div>
                                    <button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="margin: -5px 0">
                                        <span>Show  <i class="fal fa-eye ml-1"></i></span>
                                        <span class="d-none">Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                                    </button>
                                </div>
                                <div id="collapseOne" class="collapse marketingCollapseItem" aria-labelledby="headingOne" data-parent="#accordionShare">
                                    <div class="card-body bg-light pt-0 pb-0 border-top">
                                        <div class="row">
                                            @php
                                                $i = 1;
                                            @endphp
                                            @foreach ($structures as $parent_s)
                                                <div class="col-md-4 pt-3 pb-3 @if($i++ < 3) border-right @endif" id="petsPolicyBox">
                                                    <p class="greyCardHead">
                                                        <i class="fal {{ $parent_s->icon }} mr-2"></i>
                                                        {{ $parent_s->name }}
                                                    </p>

                                                    @foreach ($parent_s->children() as $s)
                                                        @if ($parent_s->group_type === 'radio')
                                                            <div class="custom-control custom-radio">
                                                                <input
                                                                        type="{{ $parent_s->group_type }}"
                                                                        id="{{ $s->id }}"
                                                                        name="{{ $parent_s->id }}"
                                                                        value="{{ $s->id }}"
                                                                        class="custom-control-input"
                                                                        {{ $s->value }}
                                                                >

                                                                <label
                                                                        class="custom-control-label"
                                                                        for="{{ $s->id }}"
                                                                >
                                                                    {{ $s->name }}
                                                                </label>
                                                            </div>

                                                            <div class="ml-4 inputSubBox" id="{{ $s->id }}">
                                                                @foreach ($s->children() as $sub_s)

                                                                    @if ($s->group_type === 'checkbox')
                                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                                            <input
                                                                                    type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    name="{{ $s->id }}[]"
                                                                                    value="{{ $sub_s->id }}"
                                                                                    id="{{ $sub_s->id }}"
                                                                                    {{ $sub_s->value }}
                                                                            >

                                                                            <label
                                                                                    class="custom-control-label"
                                                                                    for="{{ $sub_s->id }}"
                                                                            >
                                                                                {{ $sub_s->name }}
                                                                            </label>
                                                                        </div>
                                                                    @endif


                                                                    @if ($s->group_type === 'radio')
                                                                        radio
                                                                    @endif

                                                                    @if ($s->group_type === 'textarea')
                                                                        textarea
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if ($parent_s->group_type === 'checkbox')
                                                            @if ($s->group_type === 'textarea')
                                                                <div class="form-group pt-3">
                                                                    <label for="{{ $s->id }}">
                                                                        {{ $s->name }}
                                                                    </label>

                                                                    <textarea
                                                                            class="form-control"
                                                                            id="{{ $s->id }}"
                                                                            name="{{ $s->id }}"
                                                                            rows="4"
                                                                    >{{ $s->value }}</textarea>
                                                                </div>
                                                            @else
                                                                <div class="custom-control custom-checkbox">
                                                                    <input
                                                                            type="{{ $parent_s->group_type }}"
                                                                            id="{{ $s->id }}"
                                                                            name="{{ $parent_s->id }}[]"
                                                                            value="{{ $s->id }}"
                                                                            class="custom-control-input"
                                                                            {{ $s->value }}
                                                                    >

                                                                    <label
                                                                            class="custom-control-label"
                                                                            for="{{ $s->id }}"
                                                                    >
                                                                        {{ $s->name }}
                                                                    </label>
                                                                </div>
                                                            @endif

                                                            <div class="ml-4 inputSubBox" id="{{ $s->id }}">
                                                                @foreach ($s->children() as $sub_s)

                                                                    @if ($s->group_type === 'checkbox')
                                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                                            <input
                                                                                    type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    name="{{ $s->id }}[]"
                                                                                    value="{{ $sub_s->id }}"
                                                                                    id="{{ $sub_s->id }}"
                                                                                    {{ $sub_s->value }}
                                                                            >

                                                                            <label
                                                                                    class="custom-control-label"
                                                                                    for="{{ $sub_s->id }}"
                                                                            >
                                                                                {{ $sub_s->name }}
                                                                            </label>
                                                                        </div>
                                                                    @endif


                                                                    @if ($s->group_type === 'radio')
                                                                        radio
                                                                    @endif

                                                                    @if ($s->group_type === 'textarea')
                                                                        textarea
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        {{--}}
                                                        @if ($parent_s->group_type === 'textarea')
                                                            <div class="form-group">
                                                                <label for="{{ $s->id }}">
                                                                    {{ $s->name }}
                                                                </label>

                                                                <textarea
                                                                        class="form-control"
                                                                        id="{{ $s->id }}"
                                                                        name="{{ $s->id }}"
                                                                        rows="4"
                                                                >{{ $s->value }}</textarea>
                                                            </div>

                                                            <div class="ml-4 inputSubBox" id="{{ $s->id }}">
                                                                @foreach ($s->children() as $sub_s)

                                                                    @if ($s->group_type === 'checkbox')
                                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                                            <input
                                                                                    type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    name="{{ $s->id }}[]"
                                                                                    value="{{ $sub_s->id }}"
                                                                                    id="{{ $sub_s->id }}"
                                                                                    {{ $sub->value }}
                                                                            >

                                                                            <label
                                                                                    class="custom-control-label"
                                                                                    for="{{ $sub_s->id }}"
                                                                            >
                                                                                {{ $sub_s->name }}
                                                                            </label>
                                                                        </div>
                                                                    @endif


                                                                    @if ($s->group_type === 'radio')
                                                                        radio
                                                                    @endif

                                                                    @if ($s->group_type === 'textarea')
                                                                        textarea
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        {{--}}
                                                    @endforeach
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    <div class="card-footer bg-light text-right">
                                        <!--<button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Hide  <i class="fal fa-eye ml-1"></i>
                                        </button>-->

                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fal fa-check-circle mr-1"></i> Save Amenities
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card propertyForm">

                            <div class="card-header d-flex justify-content-between" id="headingTwo">
                                <div>
                                    <i class="fal fa-share-alt mr-1 h5 mb-0 d-inline-block align-middle"></i> <span class="d-inline-block align-middle">Share</span>
                                </div>
                                <!--<button class="btn btn-light btn-sm text-muted" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo" style="margin: -5px 0">
                                    <span class="d-none">Show  <i class="fal fa-eye ml-1"></i></span>
                                    <span>Hide  <i class="fal fa-eye-slash ml-1"></i></span>
                                </button>-->
                            </div>
                            <div id="collapseTwo" class="" aria-labelledby="headingTwo" data-parent="#accordionShare">
                                <div class="card-body bg-light border-top">

                                    <div class="row">
                                        <div class="col-xl-8 mb-3">
                                            <label for="applyLink">Your Public Website Link</label>
                                            <div class="input-group mb-3">
                                                <input id="applyLink" name="applyLink" type="text" class="form-control" value="{{ env('APP_ENV') === 'production' ? $unit->public_link : route('view/local_unique_link', ['unique_link' => $unit->unique_link ]) }}" disabled>
                                                <div class="input-group-append">
                                                    <button id="copyButton" class="btn btn-grey" type="button" data-toggle="tooltip" title="Copy to Clipboard" data-placement="top"><i class="fal fa-copy mr-1"></i> Copy</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-4 mb-3">
                                            <label>Invite Renters to Apply</label>
                                            <button onclick = "createPopupWin('https://www.facebook.com/sharer/sharer.php?kid_directed_site=0&u={{ urlencode($unit->public_link) }}&display=popup&ref=plugin&src=share_button',
                    'Share on Facebook', 600, 600);" class="btn btn-primary btn-block"><i class="fab fa-facebook-square mr-1"></i> Share On Facebook</button>
                                        </div>
                                        <div class="col-xl-4 mb-3">
                                            <label>&nbsp;</label>
                                            <button data-toggle="modal" data-target="#shareModal" class="btn btn-primary btn-block"><i class="fa fa-envelope mr-1"></i> Share By Email</button>
                                        </div>
                                    </div>

                                </div>
                            </div>





                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('properties/units/share/post', ['unit' => $unit->id]) }}" id="share_email_form">
                    @csrf
                    <input type="hidden" name="link" value="{{ urlencode($unit->unique_link) }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shareModalTitle">Share Unit By Email</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label for="share_yo_email_address">Email <i class="required fal fa-asterisk"></i></label>
                            <input name="email" id="share_yo_email_address" type="text" value="" class="form-control">

                            <span class="invalid-feedback" role="alert" id="validation-error-email" style="display: none;">
                                    <strong></strong>
                            </span>

                        </div>

{{--                        <div class="mb-2">--}}
{{--                            <label for="shareToMessage">Message <i class="required fal fa-asterisk"></i></label>--}}
{{--                            <textarea rows="5" type="text" class="form-control" name="message" id="shareToMessage">--}}
{{--                                --}}

{{--                            </textarea>--}}

{{--                            <span class="invalid-feedback" role="alert" id="validation-error-message" style="display: none;">--}}
{{--                                    <strong></strong>--}}
{{--                                </span>--}}

{{--                        </div>--}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Share</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>

        $( document ).ready(function() {
            $('.marketingCollapseItem').on('hide.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
            });
            $('.marketingCollapseItem').on('show.bs.collapse', function () {
                $(this).prev('div').find('button span').toggleClass('d-none');
            });

            $("#share_email_form").submit(function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(data)
                    {
                        $('#shareModal').modal('hide');
                    },
                    error: function(data) {
                        var $errors = data.responseJSON.errors
                        Object.keys($errors).forEach(i => {
                            var errBlock = $("#validation-error-" + i)
                            errBlock.children('strong').text($errors[i][0])
                            errBlock.css('display', 'block')
                        })
                    }
                });


            });

            $('#copyButton').tooltip();
            $('#copyButton').bind('click', function() {
                var copyText  = document.getElementById('applyLink');
                var temp = $("<input>");
                $("body").append(temp);
                temp.val($('#applyLink').val()).select();
                try {
                    var success = document.execCommand('copy');
                    if (success) {
                        $('#copyButton').trigger('copied', ['Copied!']);
                        temp.remove();
                    } else {
                        $('#copyButton').trigger('copied', ['Copy with Ctrl-c']);
                    }
                } catch (err) {
                    $('#copyButton').trigger('copied', ['Copy with Ctrl-c']);
                }
            });
            $('#copyButton').bind('copied', function(event, message) {
                $(this).attr('title', message)
                    .tooltip('_fixTitle')
                    .tooltip('show')
                    .attr('title', "Copy to Clipboard")
                    .tooltip('_fixTitle');
            });
        });
    </script>
    <script>
        function createPopupWin(pageURL, pageTitle,
                                popupWinWidth, popupWinHeight) {
            var left = (screen.width - popupWinWidth) / 2;
            var top = (screen.height - popupWinHeight) / 4;

            var myWindow = window.open(pageURL, pageTitle,
                'resizable=yes, width=' + popupWinWidth
                + ', height=' + popupWinHeight + ', top='
                + top + ', left=' + left);
        }
    </script>

    <script>
        $(document).ready(function () {


            $('input[type="checkbox"]').change(function () {
                if ($(this).prop('checked')) {
                    if (document.querySelector('input[value="' + $(this).attr('name').replace(/\D/g, '') + '"]'))
                        document.querySelector('input[value="' + $(this).attr('name').replace(/\D/g, '') + '"]').checked = true;
                }
                if (!$(this).prop('checked')) {
                    var list = document.querySelectorAll('input[name="' + $(this).attr('value').replace(/\D/g, '') + '"]');
                    for (var i = 0; i < list.length; i++) {
                        list[i].checked = false;
                    }

                    list = document.querySelectorAll('input[name="' + $(this).attr('value').replace(/\D/g, '') + '[]"]');
                    for (var i = 0; i < list.length; i++) {
                        list[i].checked = false;
                    }
                }
            });

            $('input[type="radio"]').change(function () {
                var _this = this;
                $('input[name="' + $(this).attr('name') + '"]').each(function () {
                    if (_this !== this) {
                        $('input[name="' + $(this).attr('value').replace(/\D/g, '') + '"]').prop('checked', false);
                        $('input[name="' + $(this).attr('value').replace(/\D/g, '') + '[]"]').prop('checked', false);
                    }
                });
            });

        });
    </script>

@endsection
