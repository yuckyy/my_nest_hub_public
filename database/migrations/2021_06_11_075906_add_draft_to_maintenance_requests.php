<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDraftToMaintenanceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->string('name', 127)->nullable()->change();
            $table->unsignedBigInteger('unit_id')->nullable()->change();
        });

        $s = new \App\Models\MaintenanceRequestStatus();
        $s->id = 4;
        $s->name = 'Draft';
        $s->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->string('name', 127)->change();
        });
        \App\Models\MaintenanceRequestStatus::find(4)->delete();
    }
}
