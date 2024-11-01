<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PropertyUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('modifier');
            $table->dropColumn('street');
            $table->dropColumn('home');
            $table->dropColumn('img');
        });

        Schema::disableForeignKeyConstraints();
        Schema::table('properties', function (Blueprint $table) {
            $table->string('type')->default('');
            $table->string('address')->default('');
            $table->string('city')->default('');
            $table->string('state', 2)->default('')->change();
            $table->string('zip', 6)->default('')->change();
            $table->timestamp('purchased', 0)->nullable();
            $table->decimal('purchased_amount', 12, 2)->default(0);
            $table->decimal('value', 12, 2)->default(0);

            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->unsignedInteger('img')->nullable();

            $table->foreign('img')->references('id')->on('files');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        Schema::disableForeignKeyConstraints();
        //
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('img');
            $table->dropSoftDeletes();

            $table->dropColumn('type');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('purchased');
            $table->dropColumn('purchased_amount');
            $table->dropColumn('value');
            $table->dropTimestamps();
        });


        Schema::table('properties', function (Blueprint $table) {
            $table->string('img', 60);
            $table->string('name', 60);
            $table->string('modifier');
            $table->string('street');
            $table->string('home');

            $table->string('state')->change();
            $table->string('zip')->change();
        });
        Schema::enableForeignKeyConstraints();
        */
    }
}
