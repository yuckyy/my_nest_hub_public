<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('document_type', ['shared_document', 'move_in_photo', 'move_out_photo'])->nullable();
            $table->string('thumbnailpath', 255)->nullable();
        });
        DB::statement("UPDATE `documents` SET `document_type` = 'shared_document'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('document_type');
        });
    }
}
