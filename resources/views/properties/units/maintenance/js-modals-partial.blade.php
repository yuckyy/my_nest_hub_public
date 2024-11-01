<script>
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });
    $(document).on('click', '.dropdown-item-click', function (e) {
        const element = document.getElementsByClassName("dropdown-menu-drop")[0];
        element.classList.remove("show");
        const element2 = document.getElementsByClassName("navbar-collapse")[0];
        element2.classList.remove("show");
    });
    if ($(window).width() < 992) {
        $('.dropdown-menu a').click(function(e){
            e.preventDefault();
            if($(this).next('.submenu').length){
                $(this).next('.submenu').toggle();
            }
            $('.dropdown').on('hide.bs.dropdown', function () {
                $(this).find('.submenu').hide();
            })

        });
    }
    $(document).on('click', '.close-click', function(event){
        document.getElementById("org_div2").innerHTML = '';
        document.getElementById("org_div1").classList.remove('d-none');
        document.getElementById("org_div1").innerHTML = '';
    });
    $(document).on('click', '.dropdown-item', function(event){
        event.stopPropagation();
        event.preventDefault();
        const category = $(this).data('category');
        const pid = $(this).data('pid');
        const category2 = $(this).text();

        function drop(){

            const categoryselected = document.getElementsByClassName("category-filter-selected");
            document.getElementById("org_div1").classList.add('d-none');
            const rootDiv = document.getElementById("org_div2")
            const elementDiv = document.createElement("div");
            const elementDiv2 = document.createElement("div");
            const elementDiv3 = document.createElement("input");
            rootDiv.classList.add('input-group');
            elementDiv.classList.add('input-group-prepend');
            elementDiv.classList.add('close-click');
            elementDiv2.classList.add('input-group-text');
            elementDiv3.classList.add('form-control');
            elementDiv3.setAttribute('name', "expense_name");
            elementDiv3.setAttribute('value', "");
            elementDiv2.innerHTML = 'X';
            elementDiv.appendChild(elementDiv2);
            rootDiv.appendChild(elementDiv3);
            rootDiv.appendChild(elementDiv);

            const rootDivv = document.getElementById("org_div1");
            const rootDivvv = document.getElementById("org_div4");
            const elementDivv = document.createElement("option");
            elementDivv.classList.add('category-filter');
            elementDivv.classList.add('category-filter-selected');

            elementDivv.selected = true;
            elementDivv.classList.add('d-none');
            elementDivv.innerHTML = category2;
            elementDivv.value = '_new';
            rootDivvv.value = pid;
            rootDivv.append(elementDivv);


        }
        if(category == undefined){

        }
        else if(category == 39){
            drop();
        }
        else if(category == 40){
            drop();
        }
        else if(category == 41){
            drop();
        }
        else{
            const categoryfilter = document.getElementsByClassName("category-filter");
            if (categoryfilter.length > 0){

                categoryfilter[0].remove();
                const rootDiv = document.getElementById("org_div1");
                const elementDiv = document.createElement("option");
                elementDiv.classList.add('category-filter');
                elementDiv.classList.add('category-filter-selected');
                elementDiv.selected = true;

                elementDiv.classList.add('d-none');
                elementDiv.innerHTML = category2;
                elementDiv.value = category;
                rootDiv.append(elementDiv)
            }else{
                const rootDiv = document.getElementById("org_div1")
                const elementDiv = document.createElement("option");
                elementDiv.classList.add('category-filter');
                elementDiv.classList.add('category-filter-selected');

                elementDiv.selected = true;
                elementDiv.classList.add('d-none');
                elementDiv.innerHTML = category2;
                elementDiv.value = category;
                rootDiv.append(elementDiv)
            }

        }

    });

</script>

<script>
    $(document).on('click', '.vacant-check-click', function(event){

        const vacantcheck = $(this).data('vacant');
        console.log(vacantcheck);
        if(vacantcheck != ''){
            const messageblock = document.getElementsByClassName("vacant-check-d")[0];
            messageblock.classList.add("d-none");
            const messageblock2 = document.getElementsByClassName("vacant-check-d")[1];
            messageblock2.classList.add("d-none");

            const elementDiv = document.createElement("span");
            elementDiv.classList.add('badge');
            elementDiv.classList.add('badge-success');
            elementDiv.classList.add('margin-left-vacant-check');
            elementDiv.innerHTML = 'Vacant';
            const propertyaddress = $(this).data('property-address');
            const maincategory = $(this).data('category');
            console.log(maincategory);
            const doc = document.getElementsByClassName("property-address")[0].innerHTML = propertyaddress;
            const doc2 = document.getElementsByClassName("property-address");
            const doc3 = document.getElementsByClassName("categoryTitle")[0].innerHTML = maincategory;
            const doc4 = document.getElementsByClassName("categoryTitle")[0].innerHTML = maincategory;
            console.log(doc3)
            doc2[0].appendChild(elementDiv);

        }else{
            const messageblock = document.getElementsByClassName("vacant-check-d")[0];
            messageblock.classList.remove("d-none");
            const messageblock2 = document.getElementsByClassName("vacant-check-d")[1];
            messageblock2.classList.remove("d-none");

            const elementDiv = document.createElement("span");
            elementDiv.classList.add('badge');
            elementDiv.classList.add('badge-danger');
            elementDiv.classList.add('margin-left-vacant-check');
            elementDiv.innerHTML = 'Occupied';
            const propertyaddress = $(this).data('property-address');
            const maincategory = $(this).data('category');
            const doc = document.getElementsByClassName("property-address")[0].innerHTML = propertyaddress;
            const doc2 = document.getElementsByClassName("property-address");
            const doc3 = document.getElementsByClassName("categoryTitle")[0].innerHTML = maincategory
            console.log(doc3)
            doc2[0].appendChild(elementDiv);
        }

    });
</script>
<script>
    jQuery( document ).ready(function($) {
        $('#documentUpload, #documentUpload2').on('change', function () {
            var uploaderWinow = $($(this).data('uploadwindow'));
            var butId = $(this).attr('id');

            var form_data = new FormData();
            form_data.append("_token", '{{ csrf_token() }}');
            var maintenanceRequestId = $("#maintenanceRequestId").val();
            if( maintenanceRequestId != ""){
                form_data.append("maintenance_request_id", maintenanceRequestId);
            }
            var ins = document.getElementById(butId).files.length;
            var sizes_ok = true;
            var num_uploaded = 0;
            for (var x = 0; x < ins; x++) {
                if(document.getElementById(butId).files[x].size > 10000000){
                    sizes_ok = false;
                } else {
                    form_data.append("documents[]", document.getElementById(butId).files[x]);
                    num_uploaded++;
                }
            }
            if((sizes_ok === false) && (num_uploaded === 0)){
                alert("File is too big");
                return;
            }

            var loadingbox =
                '<li class="loadingBox list-group-item text-center list-group-item-action list-group-item-info bg-white">' +
                '<img src="/images/loading.gif" style="margin:auto" />' +
                '</li>';
            uploaderWinow.find('.sharedFileList').append(loadingbox);
            uploaderWinow.find('.sharedFileList').parent().removeClass('d-none');

            $.ajax({
                url: '{{ route('maintenance/document-upload') }}',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    uploaderWinow.find('.loadingBox').remove();
                    if(response.maintenance_request_id){
                        $("#maintenanceRequestId").val(response.maintenance_request_id);
                    }
                    var index;
                    var docbox = '';
                    for (index = 0; index < response.uploaded.length; ++index) {
                        if(response.uploaded[index].error){
                            docbox = docbox +
                                '<li class="p-2 list-group-item list-group-item-action fileWithError list-group-item-danger">' +
                                '<a class="sharedFileLink text-danger" href="javascript:void(0)">' + response.uploaded[index].icon +
                                '<span>' + response.uploaded[index].name + '</span></a>' +
                                '<strong class="float-right text-danger">' + response.uploaded[index].error + '</strong>' +
                                '</li>';
                        } else {
                            docbox = docbox +
                                '<li class="p-2 list-group-item list-group-item-action" data-documentid="' + response.uploaded[index].id + '">' +
                                '<a class="sharedFileLink" href="' + response.uploaded[index].url + '" target="_blank">' + response.uploaded[index].icon +
                                '<span>' + response.uploaded[index].name + '</span></a>' +
                                '<button class="btn btn-sm btn-cancel deleteDocument" data-documentid="' + response.uploaded[index].id + '"><i class="fal fa-trash-alt"></i></button>' +
                                '<input type="hidden" name="document_ids[]" value="' + response.uploaded[index].id + '">' +
                                '</li>';
                        }
                    }
                    uploaderWinow.find('.sharedFileList').append(docbox);
                    window.setTimeout('$(".fileWithError").fadeOut("fast")', 3000);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });

        $(document).on('click', '.deleteDocument', function(event){
            event.stopPropagation();
            event.preventDefault();
            var documentid = $(this).data('documentid');
            var form_data = new FormData();
            form_data.append("_token", '{{ csrf_token() }}');
            form_data.append("document_id", documentid);
            $.ajax({
                url: '{{ route('maintenance/document-delete') }}',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    $('.sharedFileList').find('li[data-documentid=' + response.document_id + ']').remove();
                    $('.sharedFileList').find('a[data-documentid=' + response.document_id + ']').remove();
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });

        $('#newTicketModal').on('hide.bs.modal', function(event) {
            if( $("#maintenanceRequestId").val() !== ""){
                event.stopPropagation();
                event.preventDefault();

                var form_data = new FormData();
                form_data.append("_token", '{{ csrf_token() }}');
                form_data.append("maintenance_request_id", $("#maintenanceRequestId").val());
                $.ajax({
                    url: '{{ route('maintenance/draft-delete') }}',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        window.location.reload(true);
                    },
                    error: function (response) {
                        window.location.reload(true);
                    }
                });
            }
        });

    });
</script>
@if(!empty($draft))
    <script>
        jQuery( document ).ready(function($) {
            $('a[data-target="#newTicketModal"]').click();
        });
    </script>
@endif

<script>
    jQuery( document ).ready(function($) {

        // DELETE RECORD
        $('#confirmDeleteModal').on('click', '.btn-ok', function(e) {
            const id = $(this).data('record-id');

            const form_data = new FormData();
            form_data.append("record_id", id);
            form_data.append("_token", '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('ajax_maintenance_delete') }}',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    window.location.reload(true);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
        $('#confirmDeleteModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('.title').text(t.data('record-title'));
            $(this).find('.btn-ok').data('record-id', t.data('record-id'));
        });

        // ARCHIVE RECORD
        $('#confirmArchiveModal').on('click', '.btn-ok', function(e) {
            var id = $(this).data('record-id');

            var form_data = new FormData();
            form_data.append("record_id", id); //record_id
            form_data.append("_token", '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('ajax_maintenance_archive') }}',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    window.location.reload(true);
                    console.log(response);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
        $('#confirmArchiveModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('.title').text(t.data('record-title'));
            $(this).find('.btn-ok').data('record-id', t.data('record-id'));
        });

        function buildMessageHtml(message) {
            return '<div class="row"><div class="col-sm-4 text-left">' + message[0] + '</div><div class="col-sm-8 text-left">' + message[1] + '</div></div><div class="row pb-1"><div class="col-sm-4"></div><div class="col-sm-8"><em>' + message[2] + '</em></div></div>';
        }

        var id;

        // Show details. get related data and conversation
        $('#detailsModal').on('show.bs.modal', function(event) {
            var t = $(event.relatedTarget);
            $(this).find('.ticketTitle').text(t.data('record-title'));
            //$(this).find('.modal-title').addClass("text-" + t.data('color'));

            id = t.data('record-id');
            $("#maintenanceRequestId").val(id);
            var bgcolor = t.data('color');
            $('#coloredDescription').removeClass("alert-danger").removeClass("alert-warning").removeClass("alert-success");
            $('#coloredDescription').addClass("alert-" + bgcolor);

            var form_data = new FormData();
            form_data.append("record_id", id); //record_id
            form_data.append("_token", '{{ csrf_token() }}');

            const target = $("#detailsModal").find('.messageList');
            const documentsContent = $("#detailsModal").find('#documentsContent');
            target.addClass("loading").html("");
            $('#detailsModal').addClass("noMessages");
            $.ajax({
                url: '{{ route('ajax_maintenance_details') }}',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    const result = response.result;
                    const descr = response.description;
                    const title = response.title;
                    const list = response.list;
                    const documents = response.documents;
                    target.html("").removeClass("loading");
                    if(result==="success") {
                        if(list.length > 0) {
                            $('#detailsModal').removeClass("noMessages");
                        }
                        for (const i in list) {
                            const message = list[i];
                            target.append(buildMessageHtml(message));
                        }

                        documentsContent.html('');
                        for (const i in documents) {
                            const document = documents[i];
                            const docHtml =
                                '<li class="p-2 list-group-item list-group-item-action" data-documentid="' + document['id'] + '">' +
                                '<a class="sharedFileLink" href="' + document['url'] + '" target="_blank">' + document['icon'] +
                                '<span>' + document['name'] + '</span></a>' +
                                ((document['can_delete'] == '1') ? '<button class="btn btn-sm btn-cancel deleteDocument" data-documentid="' + document['id'] + '"><i class="fal fa-trash-alt"></i></button>' : "") +
                                '<input type="hidden" name="document_ids[]" value="' + document['id'] + '">' +
                                '</li>';
                            documentsContent.append(docHtml);
                        }
                    }
                    $("#ticketDescription").text(descr);
                    $("#ticketNumber").text(response.name);
                    $("#contactContent").text(response.contact);
                    if(response.contact){
                        $("#contactTitle, #contactContent").show()
                    } else {
                        $("#contactTitle, #contactContent").hide()
                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });

        });

        $('#sendMessage').on('click', function(e) {
            const text = $('#newMessageText').val();

            if (!text) {
                $('#modal-new-message-error').css('display', 'block');
                $($('#modal-new-message-error strong')[0]).text('The text field is required.');

                return 0;
            }

            const form_data = new FormData();
            form_data.append('id', id); //record_id
            form_data.append('text', text);
            form_data.append('_token', '{{ csrf_token() }}');

            const target = $("#detailsModal").find('.messageList');

            $.ajax({
                url: '{{ route('ajax_maintenance_add_message') }}',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (response) {
                    const { message } = response;
                    $('#newMessageText').val('');
                    target.append(buildMessageHtml(message));
                    $('#modal-new-message-error').css('display', 'none');
                    $('#detailsModal').removeClass("noMessages");
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    });
</script>
