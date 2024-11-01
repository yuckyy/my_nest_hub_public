<?php

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prop = new Property();
        $prop->img = '/images/sample-property-photo.jpg';
        $prop->name = 'Village';
        $prop->modifier = 'Vacant';
        $prop->state = 'Miami';
        $prop->street = 'FL';
        $prop->home = '17';
        $prop->zip = ' 198';
        $prop->user_id = 2;
        $prop->save();

        $prop = new Property();
        $prop->img = '';
        $prop->name = 'Stone';
        $prop->modifier = 'Occupied';
        $prop->state = 'Miami';
        $prop->street = 'FL';
        $prop->home = '18';
        $prop->zip = ' 33325';
        $prop->user_id = 2;
        $prop->save();

    }
}
