<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarketingToUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->text('additional_requirements')->nullable();
            $table->timestamp('available_date')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('monthly_rent', 12, 2)->nullable();
            $table->decimal('security_deposit', 12, 2)->nullable();
            $table->decimal('minimum_credit', 12, 2)->nullable();
            $table->decimal('minimum_income', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('additional_requirements');
            $table->dropColumn('available_date');
            $table->dropColumn('duration');
            $table->dropColumn('monthly_rent');
            $table->dropColumn('security_deposit');
            $table->dropColumn('minimum_credit');
            $table->dropColumn('minimum_income');
        });
    }
}
