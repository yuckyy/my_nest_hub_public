<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleToAddons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addons', function (Blueprint $table) {
            DB::statement('ALTER TABLE `addons` MODIFY `description` TEXT NULL;');
            $table->text('title')->nullable();
            $table->string('stripe_plan_id', 255)->nullable();
            $table->string('stripe_product_id', 255)->nullable();
            $table->boolean('active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addons', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('stripe_plan_id');
            $table->dropColumn('stripe_product_id');
            $table->dropColumn('active');
        });
    }
}
