<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function($table) {
             $table->dropColumn('base_id');
             $table->dropColumn('is_lease_pay');
             $table->unsignedBigInteger('invoice_id')->nullable();
        });

        Schema::table('payments', function($table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function($table) {
            $table->unsignedBigInteger('base_id')->nullable(); // lease ID or bill ID
            $table->boolean('is_lease_pay')->default(false);
        });
    }
}
