<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdentityIdToFinancial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->unsignedBigInteger('identity_id')->nullable();
            $table->string('funding_source_url', 255)->nullable();
        });

        DB::statement("ALTER TABLE financial MODIFY COLUMN finance_type ENUM('card', 'bank', 'stripe_account', 'paypal', 'dwolla_target', 'dwolla_source')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial', function (Blueprint $table) {
            $table->dropColumn('identity_id');
            $table->dropColumn('funding_source_url');
        });
    }
}
