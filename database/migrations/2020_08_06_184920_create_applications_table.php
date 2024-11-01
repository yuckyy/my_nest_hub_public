<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 127)->default('');
            $table->string('lastname', 127)->default('');

            $table->timestamp('dob')->nullable();

            $table->string('email', 127)->default('');
            $table->string('phone', 127)->default('');

            $table->tinyInteger('smoke')->default(0);
            $table->tinyInteger('evicted_or_unlawful')->default(0);
            $table->tinyInteger('felony_or_misdemeanor')->default(0);
            $table->tinyInteger('refuse_to_pay_rent')->default(0);

            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');

//            $table->unsignedInteger('property_id')->nullable();
//            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

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
        Schema::dropIfExists('applications');
    }
}
