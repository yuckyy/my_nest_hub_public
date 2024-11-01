<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('finance_id');
            $table->unsignedBigInteger('unit_id');
            $table->timestamps();
        });

        Schema::table('finance_units', function (Blueprint $table) {
            $table->foreign('finance_id')->references('id')->on('financial');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_units');
    }
}
