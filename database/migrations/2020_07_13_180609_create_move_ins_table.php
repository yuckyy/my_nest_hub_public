<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoveInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('move_ins', function (Blueprint $table) {
            $table->increments('id');
            $table->text('memo')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamp('due_on')->nullable();
            $table->unsignedInteger('lease_id');

            $table->foreign('lease_id')->references('id')->on('leases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('move_ins');
    }
}
