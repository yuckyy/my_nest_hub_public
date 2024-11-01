<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            // $table->foreign('user_id')
            //     ->references('id')
            //     ->on('users')->onDelete('cascade');

            $table->unsignedInteger('application_id');
            // $table->foreign('application_id')
            //     ->references('id')
            //     ->on('applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('applications_users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
