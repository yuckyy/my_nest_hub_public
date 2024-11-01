@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ url('properties') }}">
            Properties
        </a>
        >
        <a href="{{route('properties/edit', ['id' => $property->id])}}">
            {{$property->address}}
        </a>
    </div>

    <div class="container-fluid pb-4">
        @include('properties.edit-address-partial')

        <div class="container-fluid">
            <div class="row">

                <div class="col-md-9 order-md-last mb-4 mb-md-0">

                    <ul class="nav nav-tabs propertyTabs">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('properties/edit', ['property' => $property->id]) }}">Unit
                                Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('properties/expenses', ['property' => $property->id]) }}">Expenses &
                                Profit</a>
                        </li>
                        <li class="nav-item mobileActive">
                            <span class="nav-link active doccount"
                                  data-href="{{ route('properties/documents', ['property' => $property->id]) }}">Documents ({{$countDocuments}})</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('properties/operations', ['property' => $property->id]) }}">Advanced</a>
                        </li>
                    </ul>

                    <div class="propertyForm mb-4">
                        <div class="">
                            <ul id="sharedFileList"
                                class="@if($documents) list-group-item @endif   sharedFileList list-group">

                                <div class="row">
                                    @if($documents)
                                        <div class="col-6 text-left">
                                            <p class="">
                                                Document Management
                                            </p>
                                        </div>
                                    @else

                                    @endif
                                    <div class="col-6 text-right">
                                        @if($documents)
                                            <button type="button"
                                                    class="btn btn-primary btn-sm mb-2 ml-lg-auto mr-sm-3 "
                                                    data-toggle="modal" data-target="#exampleModal">
                                                Add Document
                                            </button>
                                        @else

                                        @endif
                                        {{--                                            <i class="fa-solid fa-medal"></i>--}}
                                        <!-- Modal -->
                                        <div class="modal fade " id="exampleModal" tabindex="-1" role="dialog"
                                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modalwidth modal-dialog-centered" style=""
                                                 role="document">

                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Add Document</h5>
                                                        <button type="button" class="close btn-sm" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-6 text-left">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <label for="validationCustom05">Unit <i
                                                                                class="required fal fa-asterisk"></i></label>
                                                                        <select name="unit_id" id="unitselector"
                                                                                class="custom-select inut-select"
                                                                                readonly="readonly">
                                                                            <option disabled value="" selected>Select
                                                                                Unit
                                                                            </option>
                                                                            @foreach($units as $unit)
                                                                                <option value="{{$unit->id}}"
                                                                                        data-lease="{{$unit->lease_id}}">{{$unit->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <span class="invalid-feedback d-none"
                                                                              id="invalid-feedback-unit">Please Select Unit</span>
                                                                    </div>
                                                                    {{--                                                                        <div class="col-12">--}}
                                                                    {{--                                                                            <label for="validationCustom05">Document Category <i class="required fal fa-asterisk"></i></label>--}}
                                                                    {{--                                                                            <select name="unit_id" class="custom-select inut-select" id="validationCustom05" readonly="readonly" >--}}
                                                                    {{--                                                                                <option value="" selected></option>--}}
                                                                    {{--                                                                            </select>--}}
                                                                    {{--                                                                        </div>--}}
                                                                    <div class="col-12">
                                                                        {{--                                                                            <div id="example"></div>--}}
                                                                        <label for="validationCustom05">Document
                                                                            Category <i
                                                                                class="required fal fa-asterisk"></i></label>
                                                                        <select id="doccategoryselector"
                                                                                class="custom-select inut-select">
                                                                            <option disabled value="" selected>Select
                                                                                Category
                                                                            </option>
                                                                            <optgroup label="Purchase & Sale">

                                                                                <option value="1">Offers & Addendums
                                                                                </option>
                                                                                <option value="2">Disclosures</option>
                                                                                <option value="3">Appraisals</option>
                                                                                <option value="4">Inspections</option>
                                                                                <option value="5">Title, Surveys, &
                                                                                    Plans
                                                                                </option>
                                                                                <option value="6">Closing Statements
                                                                                </option>
                                                                                <option value="7">Recorded Deeds
                                                                                </option>
                                                                            </optgroup>
                                                                            <optgroup label="Leases & Tenants">
                                                                                <option value="8">Leases & Exhibits
                                                                                </option>
                                                                                <option value="9">Apps & Credit Checks
                                                                                </option>
                                                                                <option value="10">Noties &
                                                                                    Correspondence
                                                                                </option>
                                                                                <option value="11">Move In/Out
                                                                                    Inspections
                                                                                </option>
                                                                                <option value="12">Eviction & Legal
                                                                                    Docs
                                                                                </option>
                                                                            </optgroup>
                                                                            <optgroup label="Mortgages & Loans">
                                                                                <option value="13">Mortgage & Loan
                                                                                    Agmts
                                                                                </option>
                                                                                <option value="14">Monthly Statements
                                                                                </option>
                                                                                <option value="15">Form 1098s</option>
                                                                            </optgroup>
                                                                            <optgroup label="Insurance">
                                                                                <option value="16">Quotes</option>
                                                                                <option value="17">Policy Docs</option>
                                                                                <option value="18">Certs & Binders
                                                                                </option>
                                                                            </optgroup>
                                                                        </select>
                                                                        <span class="invalid-feedback d-none"
                                                                              id="invalid-feedback-category">Please Select Category</span>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="col-6">
                                                                <div id="sharedFileList2">
                                                                    <div
                                                                        class="leaseFormFileUploadBox card-body text-center bg-white border p-2">
                                                                        <div class="filesBox">
                                                                            <div class="h1 pb-1">
                                                                                <i class="fal fa-file-alt"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input"
                                                                                   id="documentUpload" multiple>
                                                                            <div
                                                                                class="custom-file-label btn btn-sm btn-primary"
                                                                                for="documentUpload" data-browse=""><i
                                                                                    class="fal fa-upload"></i> Upload
                                                                                Documents
                                                                            </div>
                                                                        </div>
                                                                        <small>
                                                                            Upload here the attachments that you want to
                                                                            be visible to your tenants.<br>
                                                                            Maximum size: 2Mb.<br>
                                                                            Allowed file types: doc, pdf, txt, jpg, png,
                                                                            gif, xls, csv.
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        {{--                                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>--}}
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                                id="savech">Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                @if ($documents)

                                    <div class="" style="background-color: #f8f9fa!important">
                                        <br>
                                        <div class="d-none d-sm-block d-lg-block">
                                            <div class="row ">
                                                <div class="col-3 text-center">
                                                    <i class="fad fa-file-certificate" style="font-size: 60px"></i>
                                                    <br>
                                                    <p style="font-size: 1.5em">Purchase & Sale</p>
                                                </div>
                                                <div class="col-3 text-center">
                                                    <i class="fad fa-key" style="font-size: 60px"></i>
                                                    <br>
                                                    <p style="font-size: 1.5em">Leases & Tenants</p>
                                                </div>
                                                <div class="col-3 text-center">
                                                    <i class="fad fa-university" style="font-size: 60px"></i>
                                                    <br>
                                                    <p style="font-size: 1.5em">Mortgages & Loans</p>
                                                </div>
                                                <div class="col-3  text-center">
                                                    <i class="fas fa-receipt" style="font-size: 60px"></i>
                                                    <br>
                                                    <p style="font-size: 1.5em">Insurance</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row padleft" style="">
                                            <div class="col-md-6 col-xl-3 ">

                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           id="ch1" type="checkbox" value="" data-filter="category1">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Offers & Addendums--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           id="ch2" type="checkbox" value="" data-filter="category2">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Disclosures--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category3">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Appraisals--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category4">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Inspections--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category5">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Title, Surveys, & Plans--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category6">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Closing Statements--}}
                                                    </label>
                                                </div>
                                                <div class="form-check categoryFilter" data-categotyid="1">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category7">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Recorded Deeds--}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">

                                                <div class="form-check ">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category8">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Leases & Exhibits--}}
                                                    </label>
                                                </div>
                                                <div class="form-check ">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category9">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Apps & Credit Checks--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category10">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Noties & Correspondence--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category11">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Move In/Out Inspections--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category12">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Eviction & Legal Docs--}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">

                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category13">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Mortgage & Loan Agmts--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category14">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Monthly Statements--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category15">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Form 1098s--}}
                                                    </label>
                                                </div>

                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category16">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Quotes--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category17">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Policy Docs--}}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input name="category[]" class="form-check-input categoryFilter"
                                                           type="checkbox" value="" data-filter="category18">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        {{--                                                        Certs & Binders--}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    </div>
                                    <hr>

                                    @foreach ($documents as $document)

                                        <li class="list-group-item-action document-cards d-none category{{$document->document_category}}"
                                            id="docid{{ $document->id }}" data-documentid="{{ $document->id }}"
                                            data-documentcategory="{{$document->document_category}}">

                                            <a class="sharedFileLink"
                                               href="https://docs.google.com/viewer?url={{url('/')}}/storage/{{ $document->filepath }}"
                                                {{--                                           target="_blank" --}}
                                            >
                                                @if($document->extension === 'pdf')
                                                    <i class="fal fa-file-pdf"></i>
                                                @endif
                                                @if($document->extension === 'doc')
                                                    <i class="fal fa-file-word"></i>
                                                @endif
                                                @if($document->extension === 'gif')
                                                    <i class="fal fa-file-image"></i>
                                                @endif
                                                @if($document->extension === 'png')
                                                    <i class="fal fa-file-image"></i>
                                                @endif
                                                @if($document->extension === 'jpeg')
                                                    <i class="fal fa-file-image"></i>
                                                @endif
                                                @if($document->extension === 'jpg')
                                                    <i class="fal fa-file-image"></i>
                                                @endif
                                                @if($document->extension === 'docx')
                                                    <i class="fal fa-file-word"></i>
                                                @endif
                                                @if($document->extension === 'pdf')
                                                    <i class="fal fa-file-pdf"></i>
                                                @endif
                                                @if($document->extension === 'csv')
                                                    <i class="fal fa-file-csv"></i>
                                                @endif
                                                @if($document->extension === 'txt')
                                                    <i class="fal fa-file-alt"></i>
                                                @endif


                                                <span style="font-size: 14px">{{ $document->name }}</span></a>
                                            <button class="btn btn-sm btn-cancel deleteDocument"
                                                    data-documentid="{{ $document->id }}"><i
                                                    class="fal fa-trash-alt mr-1"></i> Delete
                                            </button>
                                            <input type="hidden" name="document_ids[]" value="{{ $document->id }}">
                                        </li>

                                    @endforeach
                                    <div id="docFilter">
                                        <div class="">
                                            <p class="alert alert-warning">
                                                Select at least one checkbox
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="card">

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 text-left">
                                                    <p class="">
                                                        Document Management
                                                    </p>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <button type="button"
                                                            class="btn btn-primary btn-sm mb-2 ml-lg-auto mr-sm-3 "
                                                            data-toggle="modal" data-target="#exampleModal">
                                                        Add Document
                                                    </button>
                                                </div>

                                            </div>
                                            <p class="alert alert-warning">
                                                There are no documents for this property
                                            </p>
                                        </div>
                                    </div>

                                @endif

                            </ul>
                        </div>

                    </div>

                    {{--                    <a name="invoices"></a>--}}


                </div>

                <div class="col-md-3 order-md-first">
                    @include('properties.edit-photos-partial')
                </div>

            </div>
        </div>
    </div>

    <!-- DELETE RECORD confirmation dialog-->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmDeleteModalTitle" aria-hidden="true">
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
                    <form class="remove-invoice" method="POST" action="{{ route('remove-expense') }}">
                        @csrf
                        <input type="hidden" name="expense_id" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-ok">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalTitle">Invoice Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light" id="view-box">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
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

@endsection
@section('scripts')
    <script>
        {{--        $(document).ready(function() {--}}
        {{--            var ac = [{--}}
        {{--                c: "A",--}}
        {{--                n: "A",--}}
        {{--                d: [{--}}
        {{--                    c: "B",--}}
        {{--                    n: "B"--}}
        {{--                }, {--}}
        {{--                    c: "C",--}}
        {{--                    n: "C"--}}
        {{--                }, {--}}
        {{--                    c: "D",--}}
        {{--                    n: "D"--}}
        {{--                }]--}}
        {{--            }--}}
        {{--            ]--}}
        {{--    </script>--}}
        {{--            $("#example").bsCascader({--}}
        {{--                loadData: function (n, c) {--}}
        {{--                    c(ac)--}}
        {{--                }--}}
        {{--            })--}}
        {{--            $("#example").bsCascader({--}}

        {{--                // split character--}}
        {{--                splitChar: ' ',--}}

        {{--                // button class--}}
        {{--                btnCls: 'btn-default',--}}

        {{--                // placeholder text--}}
        {{--                placeHolder: 'Select...',--}}

        {{--                // dropup instead of dropdown--}}
        {{--                dropUp: false,--}}

        {{--                // lazy load--}}
        {{--                lazy: false,--}}

        {{--                // open dropdown on hover--}}
        {{--                openOnHover: false,--}}

        {{--                // onChange callback--}}
        {{--                onChange: $.noop,--}}

        {{--                // custom selectable function--}}
        {{--                selectable: function (item) {--}}
        {{--                    return item && item.loaded && (!item.children || item.children.length <= 0 || item.selectable);--}}
        {{--                }--}}

        {{--            })--}}
        {{--        });--}}

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cookiesjs/1.4.2/cookies.min.js"></script>
    {{--    <script type="text/javascript">--}}
    {{--        $(document).ready(function() {--}}
    {{--            'use strict';--}}
    {{--            var cn = 'CheckBoxes', set = {}, cook = cookies(cn) || {};--}}
    {{--            cookies.expires = 10 * 24 * 3600;--}}

    {{--            function saveChecked() {--}}
    {{--                cook[this.id] = this.checked;--}}
    {{--                set[cn] = cook;--}}

    {{--                // Записываем в кукис текущее значение checked--}}
    {{--                cookies(set);--}}
    {{--            };--}}

    {{--            document.querySelectorAll('#ch input[type=checkbox]').forEach(function(i) {--}}
    {{--                i.onchange = saveChecked;--}}
    {{--                // Устанавливаем значение из кукиса--}}
    {{--                i.checked = !!cook[i.id];--}}
    {{--            })--}}

    {{--        })();--}}
    {{--    </script>--}}
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     const categoryCheckboxes = document.querySelectorAll('.categoryFilter');
        //     categoryCheckboxes.forEach(function(checkbox) {
        //         checkbox.checked = false;
        //     });
        // });

        $(document).ready(function () {
            function docCount() {
                const documentCards = document.querySelectorAll(".document-cards");
                const numDocumentCards = documentCards.length;
                console.log(`Количество элементов с классом document-cards: ${numDocumentCards}`);

                const docCount = document.querySelector(".doccount");
                docCount.textContent = 'Documents' + '(' + numDocumentCards + ')';
            }

            function getDocCounts() {
                const elements = document.querySelectorAll('[data-documentcategory="1"]');
                const elements2 = document.querySelectorAll('[data-documentcategory="2"]');
                const elements3 = document.querySelectorAll('[data-documentcategory="3"]');
                const elements4 = document.querySelectorAll('[data-documentcategory="4"]');
                const elements5 = document.querySelectorAll('[data-documentcategory="5"]');
                const elements6 = document.querySelectorAll('[data-documentcategory="6"]');
                const elements7 = document.querySelectorAll('[data-documentcategory="7"]');
                const elements8 = document.querySelectorAll('[data-documentcategory="8"]');
                const elements9 = document.querySelectorAll('[data-documentcategory="9"]');
                const elements10 = document.querySelectorAll('[data-documentcategory="10"]');
                const elements11 = document.querySelectorAll('[data-documentcategory="11"]');
                const elements12 = document.querySelectorAll('[data-documentcategory="12"]');
                const elements13 = document.querySelectorAll('[data-documentcategory="13"]');
                const elements14 = document.querySelectorAll('[data-documentcategory="14"]');
                const elements15 = document.querySelectorAll('[data-documentcategory="15"]');
                const elements16 = document.querySelectorAll('[data-documentcategory="16"]');
                const elements17 = document.querySelectorAll('[data-documentcategory="17"]');
                const elements18 = document.querySelectorAll('[data-documentcategory="18"]');
                const category1Element = document.getElementsByClassName('form-check-label');

                category1Element[0].textContent = `Offers & Addendums (${elements.length})`;
                category1Element[1].textContent = `Disclosures (${elements2.length})`;
                category1Element[2].textContent = `Appraisals (${elements3.length})`;
                category1Element[3].textContent = `Inspections (${elements4.length})`;
                category1Element[4].textContent = `Title, Surveys, & Plans (${elements5.length})`;
                category1Element[5].textContent = `Closing Statements (${elements6.length})`;
                category1Element[6].textContent = `Recorded Deeds (${elements7.length})`;
                category1Element[7].textContent = `Leases & Exhibits (${elements8.length})`;
                category1Element[8].textContent = `Apps & Credit Checks (${elements9.length})`;
                category1Element[9].textContent = `Noties & Correspondence (${elements10.length})`;
                category1Element[10].textContent = `Move In/Out Inspections (${elements11.length})`;
                category1Element[11].textContent = `Eviction & Legal Docs (${elements12.length})`;
                category1Element[12].textContent = `Mortgage & Loan Agmts (${elements13.length})`;
                category1Element[13].textContent = `Monthly Statements (${elements14.length})`;
                category1Element[14].textContent = `Form 1098s (${elements15.length})`;
                category1Element[15].textContent = `Quotes (${elements16.length})`;
                category1Element[16].textContent = `Policy Docs (${elements17.length})`;
                category1Element[17].textContent = `Certs & Binders (${elements18.length})`;

            }

            docCount();
            getDocCounts();
            const sharedLinks = document.querySelectorAll('.sharedFileLink');

            sharedLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    const categoryCheckboxes = document.querySelectorAll('.categoryFilter');
                    categoryCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = false;
                    });
                });
            });


            const buttons = document.querySelectorAll('.categoryFilter')
            const cards = document.querySelectorAll('.list-group-item-action')
            var docfilter = document.getElementById("docFilter");

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const currentCategory = button.dataset.filter
                    filter(currentCategory, cards, button)
                    // console.log(button.dataset.filter);
                })

                if (button.checked) {
                    const currentCategory = button.dataset.filter
                    filter(currentCategory, cards, button)
                }

                // console.log(button.dataset.filter);
                function filter(category, items, button) {
                    if (button.checked) {
                        // console.log('checked');
                        items.forEach((item) => {
                            const isItemFiltered = !item.classList.contains(category)
                            if (isItemFiltered) {
                                if (button.checked == false) {
                                    item.classList.add('d-none');
                                    docfilter.classList.remove('d-none');
                                    item.classList.remove('d-doc')
                                }
                            } else {
                                if (button.checked == true) {
                                    item.classList.remove('d-none');
                                    docfilter.classList.add('d-none');
                                    item.classList.add('d-doc')
                                }

                            }
                        })
                    } else {

                        items.forEach((item) => {
                            const isItemFiltered = item.classList.contains(category)
                            if (isItemFiltered) {
                                if (button.checked == false) {
                                    item.classList.add('d-none')

                                    item.classList.remove('d-doc')
                                    const d = document.querySelectorAll('.d-doc')
                                    // console.log(d);
                                    if (d.length > 0) {

                                        docfilter.classList.add('d-none');
                                    } else {

                                        docfilter.classList.remove('d-none');
                                    }
                                }
                            } else {
                                if (button.checked == true) {
                                    item.classList.remove('d-none')

                                }

                            }
                        })
                    }

                }
            });


            $(document).on('click', '.deleteDocument', function (event) {
                event.stopPropagation();
                event.preventDefault();
                var documentid = $(this).data('documentid');

                var document_name = $(this).parent("li").find("span").text();
                // console.log(document_name);
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
                    url: '{{ route('properties/document-delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        $(".preloader").fadeOut("fast");
                        $('.sharedFileList2').find('li[data-documentid=' + response.document_id + ']').remove();
                        $('.sharedFileList2').find('a[data-documentid=' + response.document_id + ']').remove();
                        $('#confirmFileDeleteModal').modal('hide');


                        const doc = 'docid' + documentid;

                        document.getElementById(doc).remove();
                        docCount();
                        getDocCounts();
                    },
                    error: function (response) {
                        $('#confirmFileDeleteModal').modal('hide');
                        console.log(response);
                    }
                });
            });

        });
    </script>
    <script src='{{ url('/') }}/vendor/bs-custom-file-input.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js"></script>
    <script src='{{ asset('js/validation.js') }}'></script>
    <script>
        jQuery(document).ready(function () {
            $('#savech').on('click', function () {
                location.reload();
            })
        });
    </script>
    <script>

        jQuery(document).ready(function () {
            $('#documentUpload').on('click', function () {

                const unitselector = document.getElementById('unitselector').value;
                const doccategoryselector = document.getElementById('doccategoryselector').value;
                // console.log(unitselector)
                // console.log(doccategoryselector)
                const spanunitselector = document.getElementById("invalid-feedback-unit");
                const spandoccategoryselector = document.getElementById("invalid-feedback-category");
                const doccategoryselectors = document.getElementById('doccategoryselector');
                const unitselector2 = document.getElementById('unitselector');
                if (unitselector) {
                    if (doccategoryselector) {
                        // senddoc();
                        doccategoryselectors.classList.remove('is-invalid');
                        const unitselector = document.getElementById('unitselector');
                        unitselector.classList.remove('is-invalid');
                        $('#documentUpload')[0].setAttribute('type', 'file');
                        spanunitselector.classList.add('d-none');
                        spandoccategoryselector.classList.add('d-none');

                    } else {
                        const unitselector = document.getElementById('unitselector');
                        doccategoryselectors.classList.add('is-invalid');
                        unitselector.classList.remove('is-invalid');
                        spandoccategoryselector.classList.remove('d-none');
                        $('#documentUpload')[0].removeAttribute('type');
                    }
                } else if (doccategoryselector) {
                    if (unitselector) {
                        // senddoc();
                        $('#documentUpload')[0].setAttribute('type', 'file');
                        doccategoryselectors.classList.remove('is-invalid');
                        const unitselector = document.getElementById('unitselector');
                        unitselector.classList.remove('is-invalid');

                        spanunitselector.classList.add('d-none');
                        spandoccategoryselector.classList.add('d-none');
                    } else {
                        const unitselector = document.getElementById('unitselector');
                        unitselector.classList.add('is-invalid');
                        $('#documentUpload')[0].removeAttribute('type');
                        spandoccategoryselector.classList.remove('is-invalid');
                        spanunitselector.classList.remove('d-none');
                        spandoccategoryselector.classList.add('d-none');
                    }
                    return doccategoryselector;
                    return unitselector;
                } else {
                    $('#documentUpload')[0].removeAttribute('type');
                    unitselector2.classList.add('is-invalid');
                    doccategoryselectors.classList.add('is-invalid');

                    spanunitselector.classList.remove('d-none');
                    spandoccategoryselector.classList.remove('d-none');

                    return doccategoryselector;
                    return unitselector;
                }
                return doccategoryselector;
                return unitselector;

            });

        });
        $('#documentUpload').on('change', function () {
            const unitselector = document.getElementById('unitselector').value;
            const doccategoryselector = document.getElementById('doccategoryselector').value;
            senddoc(doccategoryselector, unitselector)
        });

        function senddoc(doccategoryselector, unitselector) {
            var form_data = new FormData();
            form_data.append("_token", '{{ csrf_token() }}');
            form_data.append("document_category", doccategoryselector);
            form_data.append("unit_id", unitselector);
            var ins = document.getElementById('documentUpload').files.length;
            var sizes_ok = true;
            var num_uploaded = 0;
            for (var x = 0; x < ins; x++) {
                if (document.getElementById('documentUpload').files[x].size > 2000000) {
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
            $('#sharedFileList2').append(loadingbox);
            $('#sharedFileList2').parent().removeClass('d-none');

            $.ajax({
                url: '{{ route('properties/document-upload') }}',
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
                    $('#sharedFileList2').append(docbox);
                    window.setTimeout('$(".fileWithError").fadeOut("fast")', 3000);
                },
                error: function (response) {
                    console.log(response);
                }
            });

        }

        var uploadField = document.getElementById("expense_file");
        uploadField.onchange = function () {
            if (this.files[0].size > 5000000) {
                alert("File is too big");
                this.value = "";
                $('.customSingleFile label[for="expense_file"]').text('Upload File');
            }
            ;
            if (hasExtension('expense_file', ['.exe', '.js'])) {
                alert("File type not allowed");
                this.value = "";
                $('.customSingleFile label[for="expense_file"]').text('Upload File');
            }
        };

        function hasExtension(inputID, exts) {
            var fileName = document.getElementById(inputID).value;
            return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
        }

        function setSettings(key, val) {
            $.ajax({
                url: "{!! route('user-settings/set') !!}",
                processData: true,
                type: 'POST',
                data: {
                    '_token': '{!! csrf_token() !!}',
                    key: key,
                    value: val
                }
            });
        }


    </script>

    <script>
        $(document).ready(function () {
            $("#cancelAddBill").click(function (e) {
                e.preventDefault();
                $("#addBillContent").collapse('hide');
            });

            $("#billType").change(function () {
                var val = $(this).val();
                if (val === "_new") {
                    $("#billType").hide();
                    $("#billTypeOtherBox").show();
                    $("#billTypeOther").focus();
                }
            });
            if ($("#billType").val() === "_new") {
                $("#billType").hide();
                $("#billTypeOtherBox").show();
                $("#billTypeOther").focus();
            }
            $("#billTypeCancel").click(function (e) {
                e.preventDefault();
                $("#billType").show();
                $("#billTypeOtherBox").hide();
                $("#billType").val("");
                $("#billTypeOther").val("");
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#editModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                $.post("{{ route('edit-payments') }}", {
                    id: button.data('id'),
                }, function (datajson) {
                    $('#add-box').html(datajson.view);
                });
            });
        });
    </script>

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

            $('input[name="last12Month-2"]').change(function () {
                var val = $('input[name="last12Month-2"]:checked').val();
                if (val == 1) {
                    $('.last12Month1-2').removeClass('d-none');
                    $('.last12Month0-2').addClass('d-none');
                } else {
                    $('.last12Month0-2').removeClass('d-none');
                    $('.last12Month1-2').addClass('d-none');
                }
            });
            $('#last12Month1-2').click();
        });
    </script>

    <script>
        var expense_id = 0;
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#detailsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                if (button.data('id')) {
                    expense_id = button.data('id');
                }
                var modal = $(this);
                $.post("{{ route('view-expense') }}", {
                    id: expense_id
                }, function (datajson) {
                    $('#view-box').html(datajson.view);
                });
            });
        });
    </script>

    <script>
        function setupAjaxLoadedContent() {
            $(".collapseFilters").click(function (e) {
                e.preventDefault();
                $('.collapseFilters').tooltip('hide');
                var target = $(".filtersRow");
                target.toggleClass("active");
                if (target.hasClass("active")) {
                    $("#advanced_filter").val("1");
                } else {
                    $("#advanced_filter").val("0");
                }
            });
            $('.showViewModal').click(function (e) {
                e.stopPropagation();
                expense_id = $(this).data('id');
                var target = $(this).data('target');
                $(target).modal('show')
            });
            $('.showDeleteRecordModal').click(function (e) {
                e.stopPropagation();
                var target = $(this).data('target');
                $("#confirmDeleteModal").find('.title').text($(this).data('record-title'));
                $("#confirmDeleteModal").find("input[name='expense_id']").val($(this).data('record-id'));
                $(target).modal('show');
            });
        } //function

        jQuery(document).ready(function ($) {
            if (document.getElementById('expensesBox')) {
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id]) }}'
                    + '&r={{rand(10000000,99999999)}}'
                    //+'&parent=property' //by default-all expenses
                    + '&column=expense_date'
                    + '&order=desc';
                $("#expensesBox").load(href, function () {
                    setupAjaxLoadedContent();
                });
            }
            $(document).on("click", '#expensesBox a.page-link, #expensesBox a.sortLink', function (e) {
                e.preventDefault();//column=due_date&order=desc&
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load($(this).attr('href') + "&advanced_filter=" + $("#advanced_filter").val(), function () {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });
            $(document).on("click", '#applyFilters', function (e) {
                e.preventDefault();
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id]) }}'
                    + '&r={{rand(10000000,99999999)}}'
                    + '&advanced_filter=1'
                    + '&column=expense_date'
                    + '&order=desc'
                    + '&expense_date=' + $('#expense_date_field').val()
                    + '&name=' + $('#name_field').val()
                    + '&amount=' + $('#amount_field').val()
                    + '&monthly=' + $('#monthly_field').val()
                    + '&parent=' + $("[name='parent']:checked").val();
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load(href, function () {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });

            $(document).on("change", "[name='parent']", function (e) {
                e.preventDefault();
                var href = '{{ route('ajax-expenses',['property_id'=>$property->id, 'column'=>'expense_date', 'order'=>'desc', 'r'=>rand(10000000,99999999)]) }}'
                    + '&parent=' + $("[name='parent']:checked").val()
                    + '&column=expense_date'
                    + '&order=desc';
                $(".preloader").fadeIn("fast");
                $("#expensesBox").load(href, function () {
                    setupAjaxLoadedContent();
                    $(".preloader").fadeOut("fast");
                });
            });

            $('#monthly').prop('checked', false);
            $('#no_end_date').prop('checked', true);
            $('#endDate').prop('disabled', true);

            $('#monthlyLabel').on('click', function (e) {
                $('#collapseMonthly').collapse('toggle');
            });
            $('#collapseMonthly').on('show.bs.collapse', function (e) {
                if ($('#monthly').is(':checked')) {
                    return false;
                }
            });
            $('#no_end_date').change(function () {
                $('#endDate').val('');
                $('#endDate').prop('disabled', this.checked);
            });
        });
    </script>
@endsection
