<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMaintenanceRequestPropertyIdToUnitId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_requests', 'property_id')) {
                $table->dropForeign('maintenance_requests_property_id_foreign');
                $table->dropColumn('property_id');
            }

            $table->unsignedBigInteger('unit_id');
            //$table->foreign('units_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            //$table->dropForeign('maintenance_requests_unit_id_foreign');
            $table->dropColumn('unit_id');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
}
