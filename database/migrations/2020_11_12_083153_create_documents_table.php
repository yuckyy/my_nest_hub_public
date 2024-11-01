<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('lease_id')->nullable(); // if null then not attached to lease
            $table->unsignedBigInteger('unit_id');
            $table->string('name', 255);
            $table->string('filepath', 255);
            $table->string('extension', 7)->default('');
            $table->string('mime', 127)->default('');


            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
