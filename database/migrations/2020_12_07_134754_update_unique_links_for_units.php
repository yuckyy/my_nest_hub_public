<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUniqueLinksForUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement("DELETE FROM unique_links");
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $units = App\Models\Unit::all();
        $units->each(function ($unit, $key) {
            $unit->link()->create([
                'model_id' => $unit->id,
                'model_type' => App\Models\Unit::class,
                'link' => App\Services\UniqueLinkService::build($unit)
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
