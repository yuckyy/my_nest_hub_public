<?php

use Illuminate\Database\Seeder;
use App\Models\AmenitiesStructure;
use Illuminate\Support\Facades\DB;

class AmenitiesSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //  ----------------- Building Amenities -----------------
        $parent_structure = new AmenitiesStructure();
        $parent_structure->name = 'Building Amenities';
        $parent_structure->group_type = 'checkbox';
        $parent_structure->icon = 'fa-hotel';
        $parent_structure->sort = 30;
        $parent_structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'COMMON LAUNDRY';
        $structure->icon = 'fa-washer';
        $structure->parent = $parent_structure->id;
        $structure->sort = 10;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'TENNIS COURTS';
        $structure->icon = 'fa-tennis-ball';
        $structure->parent = $parent_structure->id;
        $structure->sort = 20;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'SECURED ENTRY';
        $structure->icon = 'fa-key';
        $structure->parent = $parent_structure->id;
        $structure->sort = 30;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'DOORMAN';
        $structure->icon = 'fa-user-cowboy';
        $structure->parent = $parent_structure->id;
        $structure->sort = 40;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'FITNESS ROOM';
        $structure->icon = 'fa-dumbbell';
        $structure->parent = $parent_structure->id;
        $structure->sort = 50;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'CLUB HOUSE';
        $structure->icon = 'fa-person-booth';
        $structure->parent = $parent_structure->id;
        $structure->sort = 60;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'ELEVATOR';
        $structure->icon = 'fa-sort-circle';
        $structure->parent = $parent_structure->id;
        $structure->sort = 70;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'YARD ACCESS';
        $structure->icon = 'fa-tree-palm';
        $structure->parent = $parent_structure->id;
        $structure->sort = 80;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'SWIMMING POOL';
        $structure->icon = 'fa-swimming-pool';
        $structure->parent = $parent_structure->id;
        $structure->sort = 90;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'GATED PROPERTY';
        $structure->icon = 'fa-hand-paper';
        $structure->parent = $parent_structure->id;
        $structure->sort = 100;
        $structure->save();

        $structure = new AmenitiesStructure();
        $structure->name = 'ON-SITE MANAGER';
        $structure->icon = 'fa-male';
        $structure->parent = $parent_structure->id;
        $structure->sort = 110;
        $structure->save();

        DB::statement("UPDATE amenities_structures SET sort=10 WHERE name='Pets Policy'");
        DB::statement("UPDATE amenities_structures SET sort=20 WHERE name='Features and Amenities'");
        DB::statement("UPDATE amenities_structures SET sort=40 WHERE name='ANY OTHER AMENITIES'");


        DB::statement("UPDATE amenities_structures SET parent=38 WHERE name='ANY OTHER AMENITIES'");
        DB::statement("UPDATE amenities_structures SET sort=120 WHERE name='ANY OTHER AMENITIES'");
        DB::statement("DELETE FROM amenities_structures WHERE name=''");


        DB::statement("UPDATE amenities_structures SET group_type='textarea' WHERE name='ANY OTHER AMENITIES'");

    }
}
