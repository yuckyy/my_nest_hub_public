@extends('layouts.app')

@section('content')

    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('maintenance') }}">Maintenance</a> > <a href="#">Service Pro</a>
    </div>
    <div class="container-fluid">


            <div class="filterApplicationsBox container-fluid">
                {{--@if (session('success'))
                    <div class="mt-5">
                        <div class="alert alert-success" role="alert">
                            {{ session()->get('success') }}
                        </div>
                    </div>
                @endif--}}
                <div class="d-block d-sm-block d-lg-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">

                    <div class="text-center text-sm-left">
                        <h1 class="h2 d-inline-block">Service Proffesional</h1>
                        <span class="badge badge-dark align-top"> {{$servicePros->count()}}total</span>
                    </div>

                    <form method="get" id="application-filter-form" action="{{ route('applications/filter') }}">

                        <div class=" btn-toolbar mb-2 mb-md-0">
                            <div class="input-group input-group-sm mr-0 mr-sm-3">
                                <input type="text" class="form-control" placeholder="Search by name" aria-label="Search by name" aria-describedby="button-addon2" id="search-field" name="q_search" value="{{ !empty(Request::get('q_search')) ? Request::get('q_search') : "" }}">
                                <div class="input-group-append">
                                    <a href="{{ route('applications') }}" class="btn btn-primary" type="button" id="button-addon2"><i class="fal fa-times"></i></a>
                                </div>
                            </div>

                            <a data-toggle="modal" data-target="#addServiceProModal" class="btn btn-primary btn-sm mr-3"><i style="color:white" class="fal fa-plus-circle mr-1"></i> <span style="color:white">Add New Service Professional</span></a>
                        </div>

                    </form>
                </div>

            </div>

            <div class="container-fluid">
                <div id="applicationsCardBox" class="applicationsCardBox mb-3">
                    @if($servicePros->count()>0)
                        @foreach($servicePros as $servicePro)
                            <div class="card applicationCard">
                                <div class="card-body p-2">

                                    <div class="applicationCardImgSell2 text-center mt-3" >
                                        <a href="http://127.0.0.1:8000/applications/38" class="applicationCardImg text-secondary" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Application">
                                            @if($servicePro->display_as_company>0)
                                                <i class="fal fa-users"></i>
                                            @else
                                                <i class="fal fa-user"></i>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="applicationCardBody fourNavIcons">
                                        <div class="ml-1 card-text" style="font-size: 1.3em">
                                            @if($servicePro->display_as_company > 0)

                                                {{$servicePro->company_name}} (company)
                                            @else
                                                {{$servicePro->first_name}} {{$servicePro->last_name}} {{$servicePro->middle_name}}
                                            @endif



                                        </div>
                                    </div>
                                    <div class="applicationAmenities text-muted" style="flex-basis:100%">
                                        <small><i class="fas fa-birthday-cake"></i><span> Email: {{$servicePro->email}}</span></small>
                                        <small>
                                            <i class="fal fa-dollar-sign"></i><span> Phone: {{$servicePro->phone}}</span>
                                        </small>
                                        <small><i class="fas fa-paw"></i><span> Category: {{$servicePro->category}}</span></small>

                                        <small><i class="fas fa-smoking"></i><span> Address: {{$servicePro->street_address}}</span></small>
                                    </div>
                                    <div class="applicationCardNav  fourNavIcons ">
                                        <span data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="38" data-record-title="Yucky Fucky's Application #38">
                                            <a href="#" data-toggle="tooltip" data-placement="top" title="" class="btn btn-sm btn-light ml-3 mr-4 text-danger" data-target-id="{{$servicePro->id}}"data-original-title="Delete Application"><i class="fal fa-trash-alt"></i><span class="d-lg-none"> Delete</span></a>
                                        </span>
                                        <a href="http://127.0.0.1:8000/maintenance/service-pro/38/view-edit" data-toggle="tooltip" data-placement="top" title="" class="btn btn-sm btn-light ml-3 mr-4 text-black" data-original-title="View/Edit Application"><i class="fal fa-wrench"></i><span class="d-lg-none"> View/Edit</span></a>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
{{--                        <div class="card applicationCard">--}}
                            <div class="card-body p-2">
                                <p class="alert alert-warning">
                                    You didn’t create any service professionals yet. Press <a href="" data-toggle="modal" data-target="#addServiceProModal"style="color:#007bff">"Add New Service Professional"</a> to create a new one.
                                </p>
                            </div>
{{--                        </div>--}}
                    @endif

                </div>
            </div>

    </div>

    <!-- DELETE RECORD confirmation dialog-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
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
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" type="submit"class="btn btn-sm btn-danger deletebutton btn-ok"><i class="fal fa-trash-alt mr-1"></i> Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade " style="padding-left: 0!important" id="addServiceProModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Service Professional</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <h6 class="mb-2 modal-title" >GENERAL INFORMATION</h6>

                    <!-- Form to fill out fields from ServicePro table -->
{{--                    <form action="{{route('service-pro/add')}}">--}}
                        <!-- First Name -->
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-4" id="displayFirstName">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <i class="required fal fa-asterisk"></i>
                                    <input type="text" class="form-control" id="firstName" name="first_name">
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-4" id="displayLastName">
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <i class="required fal fa-asterisk"></i>
                                    <input type="text" class="form-control" id="lastName" name="last_name">
                                </div>
                            </div>

                            <!-- Middle Name -->
                            <div class="col-4" id="displayMiddleName">
                                <div class="form-group">
                                    <label for="middleName">Middle Name</label>
                                    <i class="required fal fa-asterisk"></i>
                                    <input type="text" class="form-control" id="middleName" name="middle_name">
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="container-fluid">
                        <!-- Company Name -->
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group mt-2 ">
                                <div class="custom-control custom-switch  ">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch1" name="display_as_company">
                                    <label class="custom-control-label" for="customSwitch1">Display as a company ?</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 d-none" id="displayCompanyName">
                            <div class="form-group">
                                <label for="companyName">Company Name</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="companyName" name="company_name">
                            </div>
                        </div>

                        <!-- Company Website -->
                        <div class="col-4 d-none" id="displayCompanyWebsite">
                            <div class="form-group">
                                <label for="companyWebsite">Company Website</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="companyWebsite"placeholder="http://www.site.com" name="company_website">
                            </div>
                        </div>


                    </div>
                    </div>
                    <div class="container-fluid">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="companyWebsite">Email</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" placeholder="example@email.com" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="companyWebsite">Phone</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="companyWebsite">Fax</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="fax" name="fax">
                            </div>
                        </div>

                    </div>
                    </div>
                    <div class="container-fluid">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="companyWebsite">Tax Identity Type</label>
                                <select name="tax_identity_type" class="custom-select form-control @error('expense_type') is-invalid @enderror @error('expense_name') is-invalid @enderror" id="taxIdentityType">
                                    <option disabled>Select Category</option>
                                    @foreach ($taxes    as $tax)
                                        <option value="{{ $tax->name }}" {{ old('expense_type') && old('expense_type') ==  $tax->id ? 'selected' : '' }}>{{ $tax->name }}</option>
                                    @endforeach
                                </select>

                            </div>

                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="companyWebsite">Tax Payer Id</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" placeholder="xxx-xx-xxxx" id="taxPayerId" name="tax_payer_id">
                            </div>
                        </div>
                        <div class="col-4">
{{--                            <div class="form-group">--}}
{{--                                <label for="companyWebsite">Display As Company</label>--}}
{{--                                <i class="required fal fa-asterisk"></i>--}}
{{--                                <input type="text" class="form-control" id="displayAsCompany" name="display_as_company">--}}
{{--                            </div>--}}



                        </div>

                    </div>
                    </div>
                    <hr>
                    <h6 class="mb-2 modal-title" >CATEGORY</h6>
                    <div class="container-fluid">
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group">
{{--                                <label for="companyWebsite">Category</label>--}}
{{--                                <i class="required fal fa-asterisk"></i>--}}
{{--                                <input type="text" class="form-control" id="category" name="category">--}}
                                <label for="billType">Expense Type <i class="required fal fa-asterisk"></i></label>
                                <select name="expense_type" class="custom-select form-control @error('expense_type') is-invalid @enderror @error('expense_name') is-invalid @enderror" id="billType">
                                    <option disabled>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('expense_type') && old('expense_type') ==  $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                    <option value="_new" {{ old('expense_type') && old('expense_type') ==  '_new' ? 'selected' : '' }}>Other</option>
                                </select>

                                <div style="display: none" class="input-group" id="billTypeOtherBox">
                                    <input id="billTypeOther" type="text" name="expense_name" class="form-control" placeholder="Enter Expense Type" aria-label="Enter Expense Type" value="{{ old('expense_name') }}">
                                    <div class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="billTypeCancel"><i class="fal fa-times"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    </div>
                    <h6 class="mb-2 modal-title" >ADDRESS</h6>
                    <div class="container-fluid">
                    <div class="row">
                        <div class="col-7">
                            <div class="form-group">
                                <label for="companyWebsite">Street Address</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="streetAddress" name="street_address">
                            </div>

                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="companyWebsite">City</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>

                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="companyWebsite">State Region</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="stateRegion" name="state_region">
                            </div>


                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="companyWebsite">Zip</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="zip" name="zip">
                            </div>

                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="companyWebsite">Country</label>
                                <i class="required fal fa-asterisk"></i>
                                <input type="text" class="form-control" id="country" name="country">
                            </div>

                        </div>
                    </div>
                    </div>




{{--                    </form>--}}
                </div>
                <div class="modal-footer">
                    <button class="btn text-right btn-primary btn-sm mr-3 addservicepro" >Add New Service Professional</button>
                </div>
            </div>
        </div>
    </div>




@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $("#customSwitch1").removeAttr("value");
        $("#customSwitch1").attr("value", "0");
        $("#customSwitch1").click(function() {
            if ($(this).is(":checked")) {
                $(this).attr("value", "1");
                $("#displayFirstName").addClass("d-none");
                $("#displayLastName").addClass("d-none");
                $("#displayMiddleName").addClass("d-none");
                $("#displayCompanyName").removeClass("d-none");
                $("#displayCompanyWebsite").removeClass("d-none");
            } else {
                $(this).removeAttr("value");
                $(this).attr("value", "0");
                $("#displayFirstName").removeClass("d-none");
                $("#displayLastName").removeClass("d-none");
                $("#displayMiddleName").removeClass("d-none");
                $("#displayCompanyName").addClass("d-none");
                $("#displayCompanyWebsite").addClass("d-none");
            }
        });
    });
    // DELETE
    const elements = document.querySelectorAll('[data-target-id]');

    for (let element of elements) {
        element.addEventListener('click', function() {
            const value = this.getAttribute('data-target-id');
            const button = document.getElementsByClassName('deletebutton')[0];
            console.log(button);
            const href = "service-pro/"+value+"/delete";
            button.setAttribute('href', href);
            button.addEventListener('click', function() {
                $.ajax({
                    url: href,
                    dataType: 'html',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: value,
                    type: 'get',
                    success: function (response) {
                        // console.log(response);
                        // document.body.innerHTML = response;
                        location.reload();
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            })

        });
    }
// // ADD
    const element = document.getElementsByClassName('addservicepro')[0];

        element.addEventListener('click', function() {
            const value = this.getAttribute('data-target-id');
            const button = document.getElementsByClassName('deletebutton')[0];
            console.log(button);
            // const href = "service-pro/add";

            const first_name = $('#firstName').val();
            const last_name = $('#lastName').val();
            const middle_name = $('#middleName').val();
            const company_website = $('#companyWebsite').val();
            const company_name = $('#companyName').val();
            const email = $('#email').val();
            const phone = $('#phone').val();
            const fax = $('#fax').val();
            const tax_identity_type = $('#taxIdentityType').val();
            const tax_payer_id = $('#taxPayerId').val();
            const display_as_company = $('#customSwitch1').val();
            const category = $('#billType').val();
            const street_address = $('#streetAddress').val();
            const city = $('#city').val();
            const state_region = $('#stateRegion').val();
            const zip = $('#zip').val();
            const country = $('#country').val();
            console.log(display_as_company);
            let form_data = new FormData();

                form_data.append('first_name', first_name);
                form_data.append('last_name', last_name);
                form_data.append('middle_name', middle_name);
                form_data.append('company_name', company_name);
                form_data.append('company_website', company_website);
                form_data.append('email', email);
                form_data.append('phone', phone);
                form_data.append('fax', fax);
                form_data.append('tax_identity_type', tax_identity_type);
                form_data.append('tax_payer_id',tax_payer_id);
                form_data.append('display_as_company', display_as_company);
                form_data.append('category', category);
                form_data.append('street_address', street_address);
                form_data.append('city', city);
                form_data.append('state_region', state_region);
                form_data.append('zip', zip);
                form_data.append('country', country);
                form_data.append('_token', '{{ csrf_token() }}');
                validateForm();
                {{--if(isValid ===false){--}}

                {{--}else{--}}
                {{--    $.ajax({--}}
                {{--        url: '{{route('service-pro/add')}}',--}}
                {{--        dataType: 'json',--}}
                {{--        cache: false,--}}
                {{--        contentType: false,--}}
                {{--        processData: false,--}}
                {{--        data: form_data,--}}
                {{--        type: 'post',--}}
                {{--        success: function (response) {--}}
                {{--            location.reload();--}}
                {{--        },--}}
                {{--        error: function (response) {--}}
                {{--            console.log(response);--}}
                {{--        }--}}
                {{--    })--}}
                {{--}--}}


            function validateForm() {
                    if(display_as_company === '0' ){
                        const fields = [
                            "first_name",
                            "last_name",
                            "middle_name",
                            "email",
                            "phone",
                            "fax",
                            "tax_identity_type",
                            "tax_payer_id",
                            "expense_type",
                            "street_address",
                            "city",
                            "state_region",
                            "zip",
                            "country",
                        ];
                        console.log(display_as_company);
                        isVal(fields);
                    }else{
                        const fields = [
                            "email",
                            "phone",
                            "fax",
                            "tax_identity_type",
                            "company_name",
                            "company_website",
                            "tax_payer_id",
                            "expense_type",
                            "street_address",
                            "city",
                            "state_region",
                            "zip",
                            "country",
                        ];
                        // console.log(display_as_company);
                        isVal(fields);
                    }
                function isVal(fields){
                    let isValid = true;

                    fields.forEach((field) => {
                        let inputs = document.querySelectorAll(`[name="${field}"]`);
                        inputs.forEach((input) => {
                            if (input.value.trim() === "") {
                                input.classList.add("is-invalid");
                                isValid = false;

                            } else {
                                input.classList.remove("is-invalid");
                                // isValid = true;
                            }

                        });

                    });
                    if(isValid ===true){
                        $.ajax({
                            url: '{{route('service-pro/add')}}',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                location.reload();
                            },
                            error: function (response) {
                                console.log(response);
                            }
                        })
                    }
                    console.log(isValid);
                    // console.log(isValid);
                    return isValid;
                }
                return isValid;
            }
        });

</script>
<script>
    $(document).ready(function() {
        $("#cancelAddBill").click(function(e){
            e.preventDefault();
            $("#addBillContent").collapse('hide');
        });

        $("#billType").change(function(){
            var val = $(this).val();
            if (val === "_new"){
                $("#billType").hide();
                $("#billTypeOtherBox").show();
                $("#billTypeOther").focus();
            }
        });
        if ($("#billType").val() === "_new"){
            $("#billType").hide();
            $("#billTypeOtherBox").show();
            $("#billTypeOther").focus();
        }
        $("#billTypeCancel").click(function(e){
            e.preventDefault();
            $("#billType").show();
            $("#billTypeOtherBox").hide();
            $("#billType").val("");
            $("#billTypeOther").val("");
        });
    });
</script>
@endsection
