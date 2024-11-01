<?php

use Illuminate\Database\Seeder;
use App\Models\AmenitiesStructure;
use Illuminate\Support\Facades\DB;

class AmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //  ----------------- Pets Policy Start -----------------
        $parent_structure = new AmenitiesStructure();
        $parent_structure->name = 'Pets Policy';
        $parent_structure->group_type = 'radio';
        $parent_structure->icon = 'fa-paw';
        $parent_structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'PETS NEGOTIABLE';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'PETS OK';
        $structure->group_type = 'checkbox';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();


        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'CATS OK';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'DOGS OK';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();


        $structure = new AmenitiesStructure();
        $structure->name = 'NO PETS';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'DON\'T SPECIFY';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();
        // ----------------- Pets Policy End -----------------


        //  ----------------- Features and Amenities Start -----------------
        $parent_structure = new AmenitiesStructure();
        $parent_structure->name = 'Features and Amenities';
        $parent_structure->group_type = 'checkbox';
        $parent_structure->icon = 'fa-umbrella-beach';
        $parent_structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'FURNISHED OR AVAILABLE FURNISHED';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'WASHER/DRYER';
        $structure->icon = '';
        $structure->group_type = 'checkbox';
        $structure->parent = $parent_structure->id;
        $structure->save();


        // --------------------- Sub structures ---------------------------
        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'IN UNIT';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'ON SITE';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();
        // --------------------- Sub structures end ---------------------------


        $structure = new AmenitiesStructure();
        $structure->name = 'PARKING';
        $structure->icon = '';
        $structure->group_type = 'checkbox';
        $structure->parent = $parent_structure->id;
        $structure->save();


        // --------------------- Sub structures ---------------------------
        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'GARAGE';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'ON STREET';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'DRIVEWAY';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'PRIVATE LOT';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'DEDICATED SPOT';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'COVERED';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();
        // --------------------- Sub structures end ---------------------------


        $structure = new AmenitiesStructure();
        $structure->name = 'GYM/FITNESS CENTER';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'AIR CONDITIONING';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'HARDWOOD FLOORS';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'FIREPLACE';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'DISHWASHER';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'STORAGE';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'WALK-IN CLOSET';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'POOL';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'HOT TUB';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'OUTDOOR SPACE';
        $structure->icon = '';
        $structure->group_type = 'checkbox';
        $structure->parent = $parent_structure->id;
        $structure->save();


        // --------------------- Sub structures ---------------------------
        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'SHARED YARD';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'PRIVATE YARD';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'PATIO';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'BALCONY';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();

        $sub_structure = new AmenitiesStructure();
        $sub_structure->name = 'GARDEN';
        $sub_structure->icon = '';
        $sub_structure->parent = $structure->id;
        $sub_structure->save();
        // --------------------- Sub structures end ---------------------------


        $structure = new AmenitiesStructure();
        $structure->name = 'WHEELCHAIR ACCESSIBLE';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();
        // ----------------- Features and Amenities End -----------------


        // ----------------- Features and Amenities End -----------------
        $parent_structure = new AmenitiesStructure();
        $parent_structure->name = '';
        $parent_structure->group_type = 'textarea';
        $parent_structure->icon = '';
        $parent_structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'ANY OTHER AMENITIES';
        $structure->icon = '';
        $structure->parent = $parent_structure->id;
        $structure->save();
        // ----------------- Features and Amenities End -----------------

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
}
