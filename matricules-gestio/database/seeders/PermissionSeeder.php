<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Permisos de l'usuari administrador
        $permissions = [
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'force_delete_user',
            'force_delete_any_user',
            'restore_user',
            'restore_any_user',
            'replicate_user',
            'reorder_user',
            'view_any_dwelling',
            'view_dwelling',
            'create_dwelling',
            'update_dwelling',
            'delete_dwelling',
            'delete_any_dwelling',
            'force_delete_dwelling',
            'force_delete_any_dwelling',
            'restore_dwelling',
            'restore_any_dwelling',
            'replicate_dwelling',
            'reorder_dwelling',
            'view_any_person',
            'view_person',
            'create_person',
            'update_person',
            'delete_person',
            'delete_any_person',
            'force_delete_person',
            'force_delete_any_person',
            'restore_person',
            'restore_any_person',
            'replicate_person',
            'reorder_person',
            'view_any_street',
            'view_street',
            'create_street',
            'update_street',
            'delete_street',
            'delete_any_street',
            'force_delete_street',
            'force_delete_any_street',
            'restore_street',
            'restore_any_street',
            'replicate_street',
            'reorder_street',
            'view_any_teleco',
            'view_teleco',
            'create_teleco',
            'update_teleco',
            'delete_teleco',
            'delete_any_teleco',
            'force_delete_teleco',
            'force_delete_any_teleco',
            'restore_teleco',
            'restore_any_teleco',
            'replicate_teleco',
            'reorder_teleco',
        ];

        #Permisos del panel User
        /*$panelUserPermissions = [
            'view_any_group',
            'view_group',
            'view_any_url',
            'view_url',
            'create_url',
            'update_url',
            'delete_url',
            'delete_any_url',
            'restore_url',
            'restore_any_url',
            'replicate_url',
            'reorder_url',
        ];*/
        #Creem permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::firstOrCreate(['name' => 'Admin']);

        $panelUser = Role::firstOrCreate(['name' => 'Panel User']);

        $permissions = Permission::all();

        $admin->syncPermissions($permissions);

        #$panelUser->syncPermissions($panelUserPermissions);
    }
}
