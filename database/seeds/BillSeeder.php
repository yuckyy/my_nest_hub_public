<?php

use Illuminate\Database\Seeder;
use App\Models\Bill;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bill = new Bill();
        $bill->name = 'WATER';
        $bill->save();

        $bill = new Bill();
        $bill->name = 'GAS';
        $bill->save();

        $bill = new Bill();
        $bill->name = 'ELECTRIC';
        $bill->save();

        $bill = new Bill();
        $bill->name = 'PHONE';
        $bill->save();

        $bill = new Bill();
        $bill->name = 'INTERNET';
        $bill->save();

        $bill = new Bill();
        $bill->name = 'PARKING';
        $bill->save();
    }
}
