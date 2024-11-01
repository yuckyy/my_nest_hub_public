<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeToUserAddons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_addons', function (Blueprint $table) {
            $table->string('stripe_subscription_id', 255);
            $table->string('stripe_subscription_status', 255);
            $table->string('coupon_code', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_addons', function (Blueprint $table) {
            $table->dropColumn('stripe_subscription_id');
            $table->dropColumn('stripe_subscription_status');
            $table->dropColumn('coupon_code');
        });
    }
}
