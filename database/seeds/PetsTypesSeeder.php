<?php

use Illuminate\Database\Seeder;

class PetsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('pets_types')->insert(['name' => 'Dog', 'alias' => 'dog']);
        DB::table('pets_types')->insert(['name' => 'Cat', 'alias' => 'cat']);
        DB::table('pets_types')->insert(['name' => 'Another (please describe)', 'alias' => 'another (please describe)']);
    }
}
