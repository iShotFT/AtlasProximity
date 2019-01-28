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
        $p_admin_faq    = Permission::findOrCreate('a.faq');
        $p_admin_ping   = Permission::findOrCreate('a.ping');
        $p_admin_pp     = Permission::findOrCreate('a.playerping');
        $p_admin_pt     = Permission::findOrCreate('a.playertrack');
        $p_admin_lc     = Permission::findOrCreate('a.linkclick');
        $p_admin_proxt  = Permission::findOrCreate('a.proximitytrack');
        $p_admin_guild  = Permission::findOrCreate('a.guild');
        $p_user_apikey  = Permission::findOrCreate('u.apikey');

        // Roles
        $r_superadmin = Role::findOrCreate('superadmin');
        $r_superadmin->syncPermissions(Permission::all());
        $r_user = Role::findOrCreate('user');
        $r_user->syncPermissions(['u.apikey']);

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
