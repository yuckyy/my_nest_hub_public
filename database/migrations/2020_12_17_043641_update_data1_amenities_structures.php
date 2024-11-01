<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateData1AmenitiesStructures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("UPDATE amenities_structures SET icon='fa-paw' WHERE name='PETS NEGOTIABLE'");
        DB::statement("UPDATE amenities_structures SET icon='fa-paw' WHERE name='PETS OK'");
        DB::statement("UPDATE amenities_structures SET icon='fa-couch' WHERE name='FURNISHED OR AVAILABLE FURNISHED'");
        DB::statement("UPDATE amenities_structures SET icon='fa-washer' WHERE name='WASHER/DRYER'");
        DB::statement("UPDATE amenities_structures SET icon='fa-car' WHERE name='PARKING'");
        DB::statement("UPDATE amenities_structures SET icon='fa-dumbbell' WHERE name='GYM/FITNESS CENTER'");
        DB::statement("UPDATE amenities_structures SET icon='fa-fan' WHERE name='AIR CONDITIONING'");
        DB::statement("UPDATE amenities_structures SET icon='fa-leaf' WHERE name='HARDWOOD FLOORS'");
        DB::statement("UPDATE amenities_structures SET icon='fa-fireplace' WHERE name='FIREPLACE'");
        DB::statement("UPDATE amenities_structures SET icon='fa-box-full' WHERE name='STORAGE'");
        DB::statement("UPDATE amenities_structures SET icon='fa-tshirt' WHERE name='WALK-IN CLOSET'");
        DB::statement("UPDATE amenities_structures SET icon='fa-water' WHERE name='POOL'");
        DB::statement("UPDATE amenities_structures SET icon='fa-hot-tub' WHERE name='HOT TUB'");
        DB::statement("UPDATE amenities_structures SET icon='fa-cloud-sun' WHERE name='OUTDOOR SPACE'");
        DB::statement("UPDATE amenities_structures SET icon='fa-wheelchair' WHERE name='WHEELCHAIR ACCESSIBLE'");



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
