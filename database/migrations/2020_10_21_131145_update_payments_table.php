<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('pay_method');
            $table->unsignedBigInteger('finance_id')->nullable();
            $table->decimal('processing_fee', 12, 2)->default(0);

            //not ? already created in 193429
            //$table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('finance_id')->references('id')->on('financial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('pay_method', ['manually', 'stripe'])->default('manually');
            $table->dropForeign('payments_finance_id_foreign');
            $table->dropForeign('payments_invoice_id_foreign');
            $table->dropColumn('finance_id');
            $table->dropColumn('processing_fee');
        });
    }
}
