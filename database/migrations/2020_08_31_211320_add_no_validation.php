<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('residence_histories', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
            $table->string('city')->nullable()->change();
        });

        Schema::table('references', function (Blueprint $table) {
            $table->string('name', 127)->nullable()->change();
            $table->string('email', 127)->nullable()->change();
            $table->string('phone', 127)->nullable()->change();
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
