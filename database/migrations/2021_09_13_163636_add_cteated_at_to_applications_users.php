<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCteatedAtToApplicationsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications_users', function (Blueprint $table) {
            $table->timestamp('applied_at')->useCurrent();
        });
        DB::statement('UPDATE `applications_users`, `applications` SET `applications_users`.`applied_at` = `applications`.`created_at` WHERE `applications_users`.`application_id` = `applications`.`id`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications_users', function (Blueprint $table) {
            $table->dropColumn('applied_at');
        });
    }
}
