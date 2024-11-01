<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class NadyaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->id = 5;
        $user->name = 'Nadya';
        $user->lastname = 'Kanevski';
        $user->email = 'nadya08053@gmail.com';
        $user->password =  Hash::make('windows_2009');
        $user->email_verified_at = '2020-06-29 12:28:08';
        $user->save();

        DB::table('users_roles')->insert([
            'user_id' => 5,
            'role_id' => 2,
        ]);
    }
}
