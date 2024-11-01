<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 127);
            $table->text('description')->nullable();
            $table->boolean('archived')->default(false);
            $table->unsignedInteger('creator_user_id'); // tenant or landlord
            $table->unsignedInteger('status_id');
            $table->unsignedInteger('priority_id');
            $table->unsignedInteger('property_id');

            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('maintenance_request_statuses');
            $table->foreign('priority_id')->references('id')->on('maintenance_request_priorities');
            $table->foreign('property_id')->references('id')->on('properties');
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
        Schema::dropIfExists('maintenance_requests');
    }
}
