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
                    <div class="row">

                        <div class="navTabsLeftPhoto col-md-6 col-lg-4 col-xl-3">

                            <div class="card propertyFormPhoto">
                                <div class="card-header">
                                    <span>Unit photo</span>
                                    <a href="#" id="removeImage" class="removeUnitButton float-right">remove <i class="fal fa-times"></i></a>
                                </div>
                                <div class="imgBoxWrap" id="imgBoxWrap">
                                    <div class="unitCardImageBox imageInnerBox" id="unitImagePlaceholder" style="line-height: 160px">
                                        @if ($unit->imageUrl())
                                            <img src="{{ $unit->imageUrl() }}" alt="{{ $unit->address }}" class="unitCardImage">
                                        @endif
                                    </div>
                                    <div class="@if ($unit->imageUrl()) d-none @endif card-body text-center imageInnerBox">
                                        <div class="display-2" style="line-height: 160px">
                                            <i class="fal fa-image text-white"></i>
                                        </div>
                                    </div>
                                    <div class="loadingInnerBox"></div>
                                </div>
                                <div class="card-footer text-muted text-center">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="unitImage">
                                        <div class="custom-file-label btn btn-sm btn-primary" for="customFileLangHTML" data-browse=""><i class="fal fa-upload"></i> Upload photo</div>
                                    </div>
                                    <small>Recommended size: 400x400 pixels, 5mb maximum</small>
                                </div>
                            </div><!-- /propertyFormPhoto -->
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card propertyFormGallery">
                                <div class="card-header">
                                    Unit Gallery
                                </div>
                                <div class="imgBoxWrap" id="galleryBoxWrap">
                                    <div class="imageInnerBox">
                                        <div class="galleryDragBox" id="unitGalleryPlaceholder">
                                            @php ($gallery_exists = false)
                                            @foreach ($unit->gallery as $key => $gallery)
                                                @php ($gallery_exists = true)
                                                <div class="galleryItem" data-imageid="{{$gallery->id}}">
                                                    <div class="galleryItemContent" style="background-image: url({{ url('storage/property/' . $property->id . '/' . $unit->id . '/gallery/' . $gallery->filename) }})">
                                                        <div class="gallaryControl galleryDrag"><i class="fal fa-arrows-alt text-white"></i></div>
                                                        <div class="gallaryControl galleryTrash"><i class="fal fa-trash-alt text-white"></i></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="@if ($gallery_exists) d-none @endif card-body text-center p-0 imageInnerBox">
                                        <div class="display-2" style="line-height: 160px">
                                            <i class="fal fa-images text-white"></i>
                                        </div>
                                    </div>
                                    <div class="loadingInnerBox"></div>
                                </div>
                                <div class="card-footer text-muted text-center">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" multiple="multiple" id="unitGallery">
                                        <div class="custom-file-label btn btn-sm btn-primary" for="customFileLangHTML" data-browse=""><i class="fal fa-upload"></i> Upload photo</div>
                                    </div>
                                </div>
                            </div><!-- /propertyFormGallery -->
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='{{ url('/') }}/vendor/jquery-sortable.js'></script>
    <script>
        $( document ).ready(function($) {
            var group = $(".galleryDragBox").sortable({
                handle: '.galleryDrag',
                helper: 'clone',
                containerSelector: 'div.galleryDragBox',
                itemSelector: 'div.galleryItem',
                pullPlaceholder: false,
                placeholder: '<div class="galleryItem placeholder"><div class="galleryItemContent"></div></div>',
                onDragStart: function ($item, container, _super) {
                    var offset = $item.offset(),
                        pointer = container.rootGroup.pointer;

                    adjustment = {
                        left: pointer.left - offset.left,
                        top: pointer.top - offset.top
                    };

                    _super($item, container);
                },
                onDrag: function ($item, position) {
                    $item.css({
                        left: position.left - adjustment.left,
                        top: position.top - adjustment.top
                    });
                },
                onDrop: function ($item, container, _super) {
                    var data = group.sortable("serialize").get();
                    sort_array = [];
                    for (index = 0; index < data[0].length; ++index) {
                        sort_array[index] = data[0][index].imageid;
                    }
                    var sort = sort_array.join(',');
                    //var jsonString = JSON.stringify(data, null, ' ');
                    console.log(sort);

                    var form_data = new FormData();
                    form_data.append("_token", '{{ csrf_token() }}');
                    form_data.append("property_id", '{{ $property->id }}');
                    form_data.append("sort", sort);
                    $.ajax({
                        url: '{{ route('files/property/unit/gallery/sort') }}',
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            console.log('success sorting');
                        },
                        error: function (response) {
                            console.log(response);
                        }
                    });

                    _super($item, container);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#unitImage').on('change', function () {
                if(document.getElementById('unitImage').files[0].size > 5242880){
                    alert("File is too big");
                    this.value = "";
                    return;
                }
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("property_id", '{{ $property->id }}');
                form_data.append("unit_id", '{{ $unit->id }}');
                form_data.append("unit_image", document.getElementById('unitImage').files[0]);
                $('#imgBoxWrap').addClass('loading');
                $.ajax({
                    url: '{{ route('files/property/unit/image/upload') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        var url = response.uploaded[0].url;

                        var tmpImg = new Image() ;
                        tmpImg.src = url;
                        tmpImg.onload = function() {
                            $('#unitImagePlaceholder').html(
                                '<img src="' + url + '" class="card-img-top" alt="..." />'
                            );
                            $('.propertyFormPhoto').find('.card-body').addClass('d-none');
                            $('#imgBoxWrap').removeClass('loading');
                        };
                    },
                    error: function (response) {
                        console.log(response);
                        $('.propertyFormPhoto').find('.card-body').removeClass('d-none');
                        $('#imgBoxWrap').removeClass('loading');
                    }
                });
            });

            $('#removeImage').on('click', function(event){
                event.preventDefault();
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("unit_id", '{{ $unit->id }}');
                form_data.append("property_id", '{{ $property->id }}');
                $.ajax({
                    url: '{{ route('files/property/unit/image/delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        console.log('success response');
                        $('#unitImagePlaceholder').html('');
                        $('.propertyFormPhoto').find('.card-body').removeClass('d-none');
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            $('#unitGallery').on('change', function () {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("property_id", '{{ $property->id }}');
                form_data.append("unit_id", '{{ $unit->id }}');
                var ins = document.getElementById('unitGallery').files.length;
                var sizes_ok = true;
                var num_uploaded = 0;
                for (var x = 0; x < ins; x++) {
                    if(document.getElementById('unitGallery').files[x].size > 5242880){
                        sizes_ok = false;
                    } else {
                        form_data.append("property_images[]", document.getElementById('unitGallery').files[x]);
                        num_uploaded++;
                    }
                }
                if((sizes_ok === false) && (num_uploaded === 0)){
                    alert("File is too big");
                    return;
                }
                $('#galleryBoxWrap').addClass('loading');
                $.ajax({
                    url: '{{ route('files/property/unit/gallery/upload') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        console.log(response);
                        var index;
                        var imagebox = '';
                        for (index = 0; index < response.uploaded.length; ++index) {
                            imagebox = imagebox +
                                '<div class="galleryItem" data-imageid="' + response.uploaded[index].id + '">' +
                                '<div class="galleryItemContent" style="background-image: url(' + response.uploaded[index].url + ')">' +
                                '<div class="gallaryControl galleryDrag"><i class="fal fa-arrows-alt text-white"></i></div>' +
                                '<div class="gallaryControl galleryTrash"><i class="fal fa-trash-alt text-white"></i></div>' +
                                '</div>' +
                                '</div>';
                        }

                        $('#unitGalleryPlaceholder').append(imagebox);
                        setTrashEvent();
                        //$(".galleryDragBox").sortable("refresh");
                        $('.propertyFormGallery').find('.card-body').addClass('d-none');
                        $('#galleryBoxWrap').removeClass('loading');
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            });

            setTrashEvent();
        });

        function setTrashEvent(){
            $('.galleryTrash').each(function(){
                if(!$(this).data('imageid')){
                    var imageid = $(this).parent().parent().data('imageid');
                    $(this).data('imageid',imageid);

                    $(this).on('click', function(event){
                        event.preventDefault();
                        var form_data = new FormData();
                        form_data.append("_token", '{{ csrf_token() }}');
                        form_data.append("property_id", '{{ $property->id }}');
                        form_data.append("unit_id", '{{ $unit->id }}');
                        form_data.append("image_id", imageid);
                        $.ajax({
                            url: '{{ route('files/property/unit/gallery/delete') }}',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                $('.galleryItem[data-imageid=' + response.image_id + ']').remove();
                                if($('#unitGalleryPlaceholder .galleryItem').length == 0){
                                    $('.propertyFormGallery').find('.card-body').removeClass('d-none');
                                }
                            },
                            error: function (response) {
                                console.log(response);
                            }
                        });
                    });
                }
            });
        }
    </script>
@endsection
