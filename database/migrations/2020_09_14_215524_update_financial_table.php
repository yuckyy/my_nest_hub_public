<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFinancialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->dropColumn('financial');
            $table->dropColumn('lease_id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users');
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
            $table->integer('financial');
            $table->integer('lease_id');

            $table->dropForeign('financial_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
