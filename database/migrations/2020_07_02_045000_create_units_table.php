<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->default('');
            $table->decimal('square')->default(0);
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('full_bathrooms')->default(0);
            $table->unsignedTinyInteger('half_bathrooms')->default(0);
            $table->text('description');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps(0);

            $table->unsignedInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
