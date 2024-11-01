<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->unsignedInteger('lease_id')->nullable(); // if null then record is template
            $table->unsignedInteger('parent_id')->nullable(); // not null if record is based on template
            $table->decimal('value', 12, 2)->default(0);

            $table->foreign('lease_id')->references('id')->on('leases');
            $table->foreign('parent_id')->references('id')->on('bills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
