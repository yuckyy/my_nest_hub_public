<script>
    var invoice_id = 0;

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.btn-stop').click(function() {
            $('.recurring-stop').submit();
        });

        bsCustomFileInput.init();

        $('#detailsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if(button.data('id')){
                invoice_id = button.data('id');
            }
            var modal = $(this);
            $.post("{{ route('view-payments') }}", {
                id: invoice_id
            }, function(datajson){
                $('#view-box').html(datajson.view);
            });
        });
    });
</script>

@if(!empty($selectedLease))
<script>
    function setupAjaxLoadedContent(){
        $(".selectable tr input[type=checkbox]").change(function(e){
            if ($(".invoice-single:checked").length == 0) {
                $('.btn-pay-invoices').prop('disabled',true);
            } else {
                $('.btn-pay-invoices').prop('disabled',false);
            }
            if (e.target.checked){
                $(this).closest("tr").addClass("selectedRow");
            } else {
                $(this).closest("tr").removeClass("selectedRow");
            }
        });
        $(".selectable tr").click(function(e){
            if (e.target.type != 'checkbox' && e.target.tagName != 'BUTTON' && e.target.tagName != 'A'){
                var cb = $(this).find("input[type=checkbox]");
                cb.trigger('click');
            }
        });
        $("#invoice-all").change(function(e){
            if (e.target.checked){
                $(".invoice-single:not(:checked)").each(function(){
                    $(this).trigger('click');
                });
            } else {
                $(".invoice-single:checked").each(function(){
                    $(this).trigger('click');
                });
            }
            if ($(".invoice-single:checked").length == 0) {
                $('.btn-pay-invoices').prop('disabled',true);
            } else {
                $('.btn-pay-invoices').prop('disabled',false);
            }
        });
        $("#invoice-all:checked").each(function(){
            $(this).trigger('click');
        });
        $(".invoice-single:checked").each(function(){
            $(this).trigger('click');
        });
        $(".collapseFilters").click(function(e) {
            e.preventDefault();
            $('.collapseFilters').tooltip('hide');
            var target = $(".filtersRow");
            target.toggleClass("active");
            if(target.hasClass("active")){
                $("#advanced_filter").val("1");
            } else {
                $("#advanced_filter").val("0");
            }
        });
        $('.showViewModal').click(function(e){
            e.stopPropagation();
            invoice_id = $(this).data('id');
            var target = $(this).data('target');
            $(target).modal('show')
        });
        $('.showDeleteRecordModal').click(function(e){
            e.stopPropagation();
            var target = $(this).data('target');
            $("#confirmDeleteModal").find('.title').text($(this).data('record-title'));
            $("#confirmDeleteModal").find("input[name='invoice_id']").val($(this).data('record-id'));
            $(target).modal('show');
        });

        $('.withTooltip').tooltip();
    }

    jQuery( document ).ready(function($) {
        if(document.getElementById('invoicesBox')){
            var href ='{{ route('ajax-invoices',['lease_id'=>$selectedLease->id]) }}'
                +'&r={{rand(10000000,99999999)}}'
                +'&paid=unpaid'
                +'&column=due_date'
                +'&order=desc';
            $("#invoicesBox").load(href, function() {
                setupAjaxLoadedContent();
            });
        }
        $(document).on("click", '#invoicesBox a.page-link, #invoicesBox a.sortLink', function(e) {
            e.preventDefault();//column=due_date&order=desc&
            $(".preloader").fadeIn("fast");
            $("#invoicesBox").load($(this).attr('href')+"&advanced_filter="+$("#advanced_filter").val(), function() {
                setupAjaxLoadedContent();
                $(".preloader").fadeOut("fast");
            });
        });
        $(document).on("click", '#applyFilters', function(e) {
            e.preventDefault();
            var href = '{{ route('ajax-invoices',['lease_id'=>$selectedLease->id]) }}'
                +'&r={{rand(10000000,99999999)}}'
                +'&advanced_filter=1'
                +'&paid=unpaid'
                +'&column=due_date'
                +'&order=desc'
                +'&due_date='+$('#due_date_field').val()
                +'&description='+$('#description_field').val()
                +'&amount='+$('#amount_field').val()
                +'&balance='+$('#balance_field').val()
                +'&paid='+$("[name='paid']:checked").val();
            $(".preloader").fadeIn("fast");
            $("#invoicesBox").load(href, function() {
                setupAjaxLoadedContent();
                $(".preloader").fadeOut("fast");
            });
        });

        $(document).on("change", "[name='paid']", function(e) {
            e.preventDefault();
            var href = '{{ route('ajax-invoices',['lease_id'=>$selectedLease->id, 'column'=>'due_date', 'order'=>'desc', 'r'=>rand(10000000,99999999)]) }}'
                +'&paid='+$("[name='paid']:checked").val()
                +'&column=due_date'
                +'&order=desc';
            $(".preloader").fadeIn("fast");
            $("#invoicesBox").load(href, function() {
                setupAjaxLoadedContent();
                $(".preloader").fadeOut("fast");
            });
        });

    });
</script>
@endif