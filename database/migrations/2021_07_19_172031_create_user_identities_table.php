<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserIdentitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_identities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('customer_url', 255)->nullable();
            $table->string('status', 255)->nullable();

            $table->boolean('verified')->default(false);
            $table->enum('account_type', ['personal', 'soleProprietorship', 'corporation', 'llc', 'partnership'])->default('personal');

            $table->string('first_name', 127);
            $table->string('last_name', 127);
            $table->string('email', 127);
            $table->string('address', 127);
            $table->string('address_2', 127)->default('');
            $table->string('city', 127);
            $table->string('state', 2);
            $table->string('zip', 6);

            $table->datetime('dob')->nullable();
            $table->string('ssn', 6)->nullable();

            //
            $table->string('business_name', 127)->nullable();
            $table->string('business_classification', 127)->nullable();
            $table->string('ein', 127)->nullable();
            $table->string('website', 127)->nullable();
            $table->string('phone', 127)->nullable();
            //

            $table->longText('log')->nullable();
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
        Schema::dropIfExists('user_identities');
    }
}
