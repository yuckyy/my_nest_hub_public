<?php


namespace App\Repositories;

use App\Http\Requests\Tenant\ShareApplicationRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\TenantWasCreated;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function getById(int $id) {
        return User::find($id);
    }

    public function getByColumn(string $column, string $value) {
        return User::where($column, $value)->get();
    }

    public function getByEmail(string $email) {
        return User::where('email', $email)->first();
    }

    public function save(array $data, $role = null){

        $user = User::create([
            'name' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make(Str::random('8'))
        ]);

        if (!empty($role)) {
            $user->roles()->attach($role);
            $user->save();
        } elseif (!empty($data['role'])) {
            $role = Role::find($data['role']);
            $user->roles()->attach($role);
            $user->roles()->attach($role);
            $user->save();
        }

        return $user;
    }

    public function createTenantIfNotIsset(array $data, $role = null, $landlord = null) {
        if (empty($role)) return false;
        $user = User::where('email', $data['email'])->whereHas('roles', function ($q) {
            $q->where('name', 'Tenant');
        })->first();
        if (!$user) {
            $data['firstname'] = ucfirst(strtolower($data['firstname']));
            $data['lastname'] = ucfirst(strtolower($data['lastname']));
            $user = $this->save($data, $role);
            $user->notify(new TenantWasCreated($user, $landlord));
        }
        return $user;
    }

    public function update(array $data, int $id) {
        $user = User::findOrFail($id)->update($data);
        return $user->save();
    }

    public function updateColumn(string $column, string $value, int $id) {
        $user = User::findOrFail($id);
        if (!empty($user->$column)) $user->$column = $value;
        $user->save();
        return $user;
    }

    public function destroy($id) {
        return User::findOrFail($id)->delete();
    }
}
