<?php

use Illuminate\Database\Seeder;

class TaxIdentityTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tax_identity_types')->insert([
            ['name' => 'SSN'],
            ['name' => 'EIN'],
        ]);
        //
    }
}
