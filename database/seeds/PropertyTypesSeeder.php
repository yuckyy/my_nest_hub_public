<?php

use Illuminate\Database\Seeder;

class PropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('property_types')->insert(['name' => 'Apartment']);
        DB::table('property_types')->insert(['name' => 'Single family home']);
        DB::table('property_types')->insert(['name' => 'Duplex/Triplex']);
        DB::table('property_types')->insert(['name' => 'Mobile/Manufactured home']);
        DB::table('property_types')->insert(['name' => 'Dormitory']);
        DB::table('property_types')->insert(['name' => 'Commercial']);
        DB::table('property_types')->insert(['name' => 'Townhouse']);
    }
}
