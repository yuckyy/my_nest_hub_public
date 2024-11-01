<?php

use Illuminate\Database\Seeder;
use App\Models\MaintenanceRequestPriority;

class MaintenanceRequestPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $priorty = new MaintenanceRequestPriority();
        $priorty->id = 1;
        $priorty->name = 'Low';
        $priorty->save();

        $priorty = new MaintenanceRequestPriority();
        $priorty->id = 2;
        $priorty->name = 'High';
        $priorty->save();

        $priorty = new MaintenanceRequestPriority();
        $priorty->id = 3;
        $priorty->name = 'Critical';
        $priorty->save();
    }
}
