<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSourceIdTypeInFinancial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `financial` MODIFY `source_id` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `financial` MODIFY `last4` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `financial` MODIFY `holder_name` VARCHAR(191) NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial', function (Blueprint $table) {
            //
        });
    }
}
