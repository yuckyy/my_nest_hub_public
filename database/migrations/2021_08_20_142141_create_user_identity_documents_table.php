<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserIdentityDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_identity_documents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_identity_id');
            $table->string('document_type', 255);
            $table->string('name', 255);
            $table->string('filepath', 255);
            $table->string('extension', 7)->default('');
            $table->string('mime', 127)->default('');
            $table->string('thumbnailpath', 255)->nullable();

            $table->string('dwolla_document_url', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->string('failure_eason', 255)->nullable();
            $table->string('failure_description', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_identity_documents');
    }
}
