<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $user = new User();
        $user->id = 1;
        $user->name = 'Super';
        $user->lastname = 'Admin';
        $user->email = 'admin@MYNESTHUB.com';
        $user->password = Hash::make('windows_2009');
        $user->email_verified_at = '2020-06-29 12:28:08';
        $user->save();

        $user = new User();
        $user->id = 2;
        $user->name = 'landlord';
        $user->lastname = 'qqq';
        $user->email = 'landlord@mail.ru';
        $user->password = Hash::make('12345678a');
        $user->email_verified_at = '2020-06-29 12:28:08';
        $user->save();

        $user = new User();
        $user->id = 3;
        $user->name = 'manager';
        $user->lastname = 'qqq';
        $user->email = 'manager@mail.ru';
        $user->password = Hash::make('12345678a');
        $user->email_verified_at = '2020-06-29 12:28:08';
        $user->save();

        $user = new User();
        $user->id = 4;
        $user->name = 'tenant';
        $user->lastname = 'qqq';
        $user->email = 'tenant@mail.ru';
        $user->password = Hash::make('12345678a');
        $user->email_verified_at = '2020-06-29 12:28:08';
        $user->save();
    }
}
