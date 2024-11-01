<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 127)->default('');
            $table->string('lastname', 127)->default('');
            $table->string('email', 127)->default('');
            $table->unsignedBigInteger('unit_id');

            $table->foreign('unit_id')->references('id')->on('units');

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedTinyInteger('monthly_due_date')->default(1);
            $table->timestamp('recurring_rent_starts_on')->nullable();
            $table->decimal('amount', 12, 2)->default(0);

            // assistance payments
            $table->decimal('section8', 12, 2)->default(0);
            $table->decimal('military', 12, 2)->default(0);
            $table->decimal('other', 12, 2)->default(0);

            $table->timestamp('prorated_rent_due')->nullable();
            $table->decimal('prorated_rent_amount', 12, 2)->default(0);

            // automatic late fees
            $table->unsignedTinyInteger('late_fee_day')->default(1);
            $table->decimal('late_fee_amount', 12, 2)->default(0);

            // security deposit
            $table->timestamp('security_deposit')->nullable();
            $table->decimal('security_amount', 12, 2)->default(0);

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
        Schema::dropIfExists('leases');
    }
}
