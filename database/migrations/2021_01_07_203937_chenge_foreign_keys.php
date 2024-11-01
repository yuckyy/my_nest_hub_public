<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChengeForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('users_roles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('finance_units', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['unit_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('user_addons', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });

        Schema::table('amenities', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
        Schema::table('unit_image_gallery', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });

        Schema::table('image_gallery', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['file_id']);

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['lease_id']);
            $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');
        });

        Schema::table('move_ins', function (Blueprint $table) {
            $table->dropForeign(['lease_id']);
            $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
