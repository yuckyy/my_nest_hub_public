<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DB::table('subscription_options')->insert([
            'name' => 'Rent Collection',
        ]);
        DB::table('subscription_options')->insert([
            'name' => 'Payment Processing',
        ]);
        DB::table('subscription_options')->insert([
            'name' => 'Email Support',
        ]);
        DB::table('subscription_options')->insert([
            'name' => 'Phone Support',
        ]);
        DB::table('subscription_options')->insert([
            'name' => 'Free migration',
        ]);
        DB::table('subscription_options')->insert([
            'name' => 'Free Custom Import',
        ]);
    }
}
