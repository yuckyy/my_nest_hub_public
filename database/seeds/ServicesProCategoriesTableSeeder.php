<?php

use Illuminate\Database\Seeder;

class ServicesProCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services_pro_categories')->insert([
            ['name' => 'Legal/Attorney'],
            ['name' => 'Handyman/Repair'],
        ]);
        //
    }
}
