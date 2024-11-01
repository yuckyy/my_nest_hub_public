<div class="card propertyFormPhoto">
    <div class="card-header" id="propertyImageIsset">
        <span>Property Photo</span>
        @if ($property->imageUrl())
        <a href="#" id="removeImage" onclick="removeImage()" class="removeUnitButton float-right">remove <i class="fal fa-times"></i></a>
        @endif
    </div>
    <div class="imgBoxWrap" id="imgBoxWrap">
        <div class="imageInnerBox" id="propertyImagePlaceholder">
            @if ($property->imageUrl())
                <img src="{{ $property->imageUrl() }}" alt="{{ $property->address }}" class="card-img-top">
            @endif
        </div>
        <div class="@if ($property->imageUrl()) d-none @endif card-body text-center imageInnerBox">
            <div class="display-2">
                <i class="fal fa-image text-white"></i>
            </div>
        </div>
        <div class="loadingInnerBox"></div>
    </div>
    <div class="card-footer text-muted text-center">
        <div class="custom-file">
            <input type="file" class="custom-file-input" name="propertyImage" id="propertyImage">
            <div class="custom-file-label btn btn-sm btn-primary" for="propertyImage" data-browse=""><span></span><i class="fal fa-upload"></i> Upload Photo</div>
        </div>
        <small>Recommended size: 400x400 pixels, 5mb maximum</small>
    </div>
</div><!-- /propertyFormPhoto -->

<div class="card propertyFormGallery mt-3">
    <div class="card-header">
        Property Gallery
    </div>
    <div class="imgBoxWrap" id="galleryBoxWrap">
        <div class="galleryDragBoxWrapper imageInnerBox">
            <div class="galleryDragBox" id="propertyGalleryPlaceholder">
                @php ($gallery_exists = false)
                @foreach ($property->gallery as $key => $gallery)
                    @php ($gallery_exists = true)
                    <div class="galleryItem" data-imageid="{{$gallery->id}}">
                        <div class="galleryItemContent" style="background-image: url({{ url('storage/property/' . $property->id . '/gallery/' . $gallery->filename) }})">
                            <div class="gallaryControl galleryDrag"><i class="fal fa-arrows-alt text-white"></i></div>
                            <div class="gallaryControl galleryTrash"><i class="fal fa-trash-alt text-white"></i></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="@if ($gallery_exists) d-none @endif card-body text-center imageInnerBox">
            <div class="display-2">
                <i class="fal fa-images text-white"></i>
            </div>
        </div>
        <div class="loadingInnerBox"></div>
    </div>
    <div class="card-footer text-muted text-center">
        <div class="custom-file">
            <input type="file" class="custom-file-input" multiple="multiple" name="propertyGallery" id="propertyGallery">
            <div class="custom-file-label btn btn-sm btn-primary" for="propertyGallery" data-browse=""><span></span><i class="fal fa-upload"></i> Upload Photos</div>
        </div>
    </div>
</div><!-- /propertyFormGallery -->

@section('scripts_in_modules')
    <script src='{{ url('/') }}/vendor/jquery-sortable.js'></script>
    <script>
        /* image gallery scripts */
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
                        url: '{{ route('files/property/gallery/sort') }}',
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
        $(document).ready(function() {
            $('#propertyImage').on('change', function () {
                if(document.getElementById('propertyImage').files[0].size > 5242880){
                    alert("File is too big");
                    this.value = "";
                    return;
                }
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("property_id", '{{ $property->id }}');
                form_data.append("property_image", document.getElementById('propertyImage').files[0]);
                $('#imgBoxWrap').addClass('loading');
                $.ajax({
                    url: '{{ route('files/property/image/upload') }}',
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
                            $('#propertyImagePlaceholder').html(
                                '<img src="' + url + '" class="card-img-top" alt="..." />'
                            );
                            var element=document.getElementById('removeImage');

                            if(!element){
                                $('#propertyImageIsset').html(
                                    '<a href="#" id="removeImage" onclick="removeImage()" class="removeUnitButton float-right">remove <i class="fal fa-times"></i></a>'
                                );
                            } else {

                            };




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

            $('#propertyGallery').on('change', function () {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("property_id", '{{ $property->id }}');
                var ins = document.getElementById('propertyGallery').files.length;
                var sizes_ok = true;
                var num_uploaded = 0;
                for (var x = 0; x < ins; x++) {
                    if(document.getElementById('propertyGallery').files[x].size > 5242880){
                        sizes_ok = false;
                    } else {
                        form_data.append("property_images[]", document.getElementById('propertyGallery').files[x]);
                        num_uploaded++;
                    }
                }
                if((sizes_ok === false) && (num_uploaded === 0)){
                    alert("File is too big");
                    return;
                }
                $('#galleryBoxWrap').addClass('loading');
                $.ajax({
                    url: '{{ route('files/property/gallery/upload') }}',
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

                        $('#propertyGalleryPlaceholder').append(imagebox);
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
                        form_data.append("image_id", imageid);
                        $.ajax({
                            url: '{{ route('files/property/gallery/delete') }}',
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            type: 'post',
                            success: function (response) {
                                $('.galleryItem[data-imageid=' + response.image_id + ']').remove();
                                if($('#propertyGalleryPlaceholder .galleryItem').length == 0){
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
        function removeImage() {
                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("property_id", '{{ $property->id }}');
                $.ajax({
                    url: '{{ route('files/property/image/delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        document.getElementById("removeImage").remove();
                        console.log('success response');
                        $('#propertyImagePlaceholder').html('');
                        $('.propertyFormPhoto').find('.card-body').removeClass('d-none');
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
        }
    </script>
@endsection
