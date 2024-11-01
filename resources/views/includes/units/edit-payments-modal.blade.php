<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Mark As Paid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('add-payment') }}" id="add_payment">
                @csrf
                <div class="modal-body bg-light" id="add-box">

                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header"><i class="fal fa-bell"></i>&nbsp;&nbsp; When to use manual edit?</div>
                        <div class="card-body">
                            <p class="card-text">
                                If you received or planning to receive funds not through out MYNESTHUB platform and
                                would like to mark this payment as "amount received" - this feature is designed
                                specifically for this purpose.
                            </p>
                        </div>
                    </div>

                    <div class="h4 pb-3 border-bottom"><strong id="inputDescription">-</strong></div>
                    <div class="row pt-2 pb-3">
                        <div class="col-sm-6">
                            Due Date: <strong class="float-right" id="inputDueDate">-</strong>
                        </div>
                        <div class="col-sm-6">
                            Bill Amount: <strong class="float-right" id="inputBillAmount">-</strong>
                        </div>
                    </div>
                    <div class="row justify-content-between pb-3">
                        <div class="col-md-6">
                            <label for="inputPaidOn">Paid On <i class="required fal fa-asterisk"></i></label>
                            <input name="payment_paid_on" id="inputPaidOn" type="date" value="2019-08-19"
                                   class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="inputPaidAmount">Paid Amount <i class="required fal fa-asterisk"></i></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                                <input type="text" class="form-control" name="paid_amount" data-type="currency"
                                       id="inputPaidAmount">
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="inputNote">Note</label>
                        <textarea type="text" class="form-control" name="payment_note" id="inputNote"></textarea>
                    </div>

                </div>
                <div class="modal-footer d-sm-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-cancel" data-dismiss="modal"><i
                            class="fal fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fal fa-check-circle mr-1"></i> Save
                        changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
