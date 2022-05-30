<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

use App\Models\User;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      $permissions = [
         // Role Permissions
         'role-list',
         'role-create',
         'role-edit',
         'role-delete',
         // User Permissions
      ];

      foreach ($permissions as $permission) {
         if (Permission::where('name', $permission)->count() > 0) {
         } else {
            Permission::create(['name' => $permission]);
         }
      }
   }
}
