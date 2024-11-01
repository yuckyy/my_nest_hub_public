<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixPropertiesStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('purchased');
            $table->dropColumn('purchased_amount');
            $table->dropColumn('value');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->timestamp('purchased', 0)->nullable();
            $table->decimal('purchased_amount', 12, 2)->default(0)->nullable();
            $table->decimal('value', 12, 2)->default(0)->nullable();
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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('purchased');
            $table->dropColumn('purchased_amount');
            $table->dropColumn('value');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->timestamp('purchased', 0);
            $table->decimal('purchased_amount', 12, 2)->default(0);
            $table->decimal('value', 12, 2)->default(0);
        });
        Schema::enableForeignKeyConstraints();
        */
    }
}
