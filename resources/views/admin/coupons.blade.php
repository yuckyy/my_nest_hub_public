@extends('layouts.app')

@section('content')
    <div class="container-fluid d-none d-md-block breadCrumbs">
        <a href="{{ route('profile') }}">Admin</a> > <a href="#">Coupons</a>
    </div>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="d-block d-sm-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3">
                <div class="text-center text-sm-left">
                    <h1 class="h2 d-inline-block">Coupons</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            {!! $grid !!}
        </div>
    </div>

    <div class="modal fade" id="showCouponUsers" tabindex="-1" role="dialog" aria-labelledby="showCouponUsersTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center m-auto">Users who used this coupon</h5>
                </div>
                <div class="modal-body bg-light">
                    <div id="showUsersContent"></div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-sm btn-cancel m-auto" data-dismiss="modal"><i class="fal fa-times mr-1"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".show-coupon-users").click(function(e) {
            e.preventDefault();
            var coupoId = $(this).data('coupon_id');
            $('#showCouponUsers').modal('show');
            $.post("{{ route('ajax-get-users') }}", {
                id: coupoId,
            }, function(datajson){
                $('#showUsersContent').html(datajson.view);
            });
        });
    });
</script>
@endsection
