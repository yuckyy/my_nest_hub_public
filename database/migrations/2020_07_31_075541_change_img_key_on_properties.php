<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImgKeyOnProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign('properties_img_foreign');
            $table->foreign('img')->references('id')->on('files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::disableForeignKeyConstraints();
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign('properties_img_foreign');
            // $table->foreign('img')->references('id')->on('files');
        });
        Schema::enableForeignKeyConstraints();
    }
}
