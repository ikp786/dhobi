<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name'       => 'Admin',
            'email'      => 'admin@gmail.com',
            'mobile'     => 111,
            'role'      => config('constant.ROLE.ADMIN'),
            'password' => Hash::make('Admin@123'),
        ]);
        $role = Role::find(1);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
