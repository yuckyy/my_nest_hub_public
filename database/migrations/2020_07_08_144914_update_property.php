<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->dropColumn('type');
        });

        Schema::disableForeignKeyConstraints();
        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedInteger('state_id')->nullable();
            $table->unsignedInteger('property_type_id')->nullable();

            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('property_type_id')->references('id')->on('property_types');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::disableForeignKeyConstraints();
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign('properties_state_id_foreign');
            $table->dropForeign('properties_property_type_id_foreign');

            $table->dropColumn('state_id');
            $table->dropColumn('property_type_id');
        });
        Schema::enableForeignKeyConstraints();

        Schema::table('properties', function (Blueprint $table) {
            $table->string('state', 2)->default('');
            $table->string('type')->default('');
        });
    }
}
