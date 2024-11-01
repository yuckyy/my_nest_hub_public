<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddControllerToUserIdentities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_identities', function (Blueprint $table) {
            $table->string('controller_first_name', 255)->nullable();
            $table->string('controller_last_name', 255)->nullable();
            $table->string('controller_title', 255)->nullable();
            $table->string('controller_address', 255)->nullable();
            $table->string('controller_address_2', 255)->nullable();
            $table->string('controller_city', 255)->nullable();
            $table->string('controller_state', 16)->nullable();
            $table->string('controller_zip', 16)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_identities', function (Blueprint $table) {
            $table->dropColumn('controller_first_name');
            $table->dropColumn('controller_last_name');
            $table->dropColumn('controller_title');
            $table->dropColumn('controller_address');
            $table->dropColumn('controller_address_2');
            $table->dropColumn('controller_city');
            $table->dropColumn('controller_state');
            $table->dropColumn('controller_zip');
        });
    }
}
