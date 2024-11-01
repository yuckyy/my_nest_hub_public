<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->unsignedInteger('property_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('expense_date');
            $table->boolean('monthly')->default(false);
            $table->enum('created_with', ['individually', 'batch', 'cron'])->default('individually');

            $table->timestamps();
        });

        Schema::table('expenses', function($table) {
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
