<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToFinancialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->string('nickname');
            $table->integer('finance_order')->default(0);
            $table->enum('finance_type', ['card', 'bank'])->default('card');
            $table->string('source_id'); // bank account Id from stripe ACH or card source ID from netevia
            $table->string('last4');
            $table->string('holder_name');
            $table->string('fingerprint')->nullable();
            $table->string('exp_date')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->dropColumn('finance_type');
            $table->dropColumn('source_id');
        });
    }
}
