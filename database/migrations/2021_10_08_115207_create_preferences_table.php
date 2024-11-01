<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('unsubscribe_token')->nullable();

            //landlord
            $table->boolean('notify_if_leases_about_to_end')->default(true);
            $table->boolean('notify_if_leases_ended')->default(true);
            $table->boolean('notify_if_tenants_not_pay')->default(true);
            $table->boolean('notify_if_late_fees_applied')->default(true);
            //tenant
            $table->boolean('notify_if_rent_is_due_soon')->default(true);
            //both
            $table->boolean('notify_if_calendar_event')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preferences');
    }
}
