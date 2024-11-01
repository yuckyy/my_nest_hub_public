<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImgKeyOnGallery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image_gallery', function (Blueprint $table) {
            $table->dropForeign('image_gallery_file_id_foreign');
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
        Schema::table('image_gallery', function (Blueprint $table) {
            $table->dropForeign('image_gallery_file_id_foreign');
            $table->foreign('file_id')->references('id')->on('files');
        });
    }
}
