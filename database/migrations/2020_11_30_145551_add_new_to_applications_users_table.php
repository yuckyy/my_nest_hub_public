<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewToApplicationsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications_users', function (Blueprint $table) {
            $table->boolean('is_new')->default(true);
        });
        DB::statement('UPDATE `applications_users` SET `is_new` = FALSE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications_users', function (Blueprint $table) {
            $table->dropColumn('is_new');
        });
    }
}
