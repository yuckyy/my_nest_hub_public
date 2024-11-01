<?php

use Illuminate\Database\Seeder;
use App\Models\MaintenanceRequestStatus;

class MaintenanceRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $s = new MaintenanceRequestStatus();
        $s->id = 1;
        $s->name = 'New';
        $s->save();

        $s = new MaintenanceRequestStatus();
        $s->id = 2;
        $s->name = 'In Progress';
        $s->save();

        $s = new MaintenanceRequestStatus();
        $s->id = 3;
        $s->name = 'Resolved';
        $s->save();

    }
}
