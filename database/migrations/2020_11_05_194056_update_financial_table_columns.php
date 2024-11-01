<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFinancialTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->dropColumn('finance_type');
        });
        Schema::table('financial', function (Blueprint $table) {
            $table->enum('finance_type', ['card', 'bank', 'stripe_account'])->default('card');
            $table->boolean('connected')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->dropColumn('connected');
        });
    }
}
