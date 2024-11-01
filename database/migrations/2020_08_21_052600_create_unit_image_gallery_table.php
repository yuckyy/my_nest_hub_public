<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitImageGalleryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_image_gallery', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedInteger('file_id');
            $table->integer('sort')->default(0);

            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_image_gallery');
    }
}
