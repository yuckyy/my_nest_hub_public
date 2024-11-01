<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceRequestMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_request_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('text')->nullable();
            $table->unsignedInteger('maintenance_request_id');
            $table->unsignedInteger('creator_user_id'); // tenant or landlord

            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->foreign('maintenance_request_id')->references('id')->on('maintenance_requests');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_request_messages');
    }
}
