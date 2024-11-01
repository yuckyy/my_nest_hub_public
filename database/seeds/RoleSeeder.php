<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role = new Role();
        $role->id = 1;
        $role->name = 'Admin';
        $role->save();


        $role = new Role();
        $role->id = 2;
        $role->name = 'Landlord';
        $role->save();

        $role = new Role();
        $role->id = 3;
        $role->name = 'Property manager';
        $role->save();

        $role = new Role();
        $role->id = 4;
        $role->name = 'Tenant';
        $role->save();
    }
}
