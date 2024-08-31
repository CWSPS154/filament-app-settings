<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentAppSettings\Database\Seeders;

use CWSPS154\FilamentUsersRolesPermissions\Models\Permission;
use Illuminate\Database\Seeder;

class AppSettingsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = Permission::create([
            'name' => 'App Settings',
            'identifier' => 'app-settings',
            'route' => null,
            'parent_id' => null,
            'status' => true
        ])->id;

        Permission::create([
            'name' => 'View & Edit App Settings',
            'identifier' => 'view-edit-settings',
            'route' => 'filament.admin.pages.app-settings',
            'parent_id' => $id,
            'status' => true
        ]);
    }
}
