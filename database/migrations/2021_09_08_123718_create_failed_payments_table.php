<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('note')->nullable();
            $table->timestamp('pay_date')->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('finance_id')->nullable();
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->string('payment_method', 255)->nullable();
            $table->string('status', 32)->nullable();
            $table->text('log')->nullable();
            $table->string('correlation_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failed_payments');
    }
}
