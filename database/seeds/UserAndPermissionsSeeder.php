<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permissions
        $p_admin_update = Permission::findOrCreate('a.update');

        // Roles
        $r_superadmin = Role::findOrCreate('superadmin');
        $r_superadmin->syncPermissions(Permission::all());

        // Users
        $test_admin = array(
            'iShot',
            'averbanck1992@gmail.com',
            '$2y$10$g/7x6Tvr41Qi1VODXPjne.hhRtTpx7hTrymNoAC9CyAPRMbfdkWs2',
        );
        $iShot      = \App\User::updateOrCreate([
            'email' => $test_admin[1],
        ], [
            'name'     => $test_admin[0],
            'password' => $test_admin[2],
        ])->syncRoles('superadmin');
    }
}
