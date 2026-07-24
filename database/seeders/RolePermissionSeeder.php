<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions based on documentation
        $permissions = [
            'pages.view', 'pages.create', 'pages.update', 'pages.publish',
            'posts.view', 'posts.create', 'posts.update', 'posts.publish',
            'events.manage', 
            'publications.manage', 'publications.publish',
            'services.view', 'services.manage',
            'facilities.view', 'facilities.manage', 'facilities.verify',
            'forum.manage',
            'aspirations.view', 'aspirations.manage', 'aspirations.publish',
            'complaints.view', 'complaints.assign', 'complaints.follow_up', 'complaints.close', 'complaints.export',
            'statistics.view', 'statistics.manage', 'statistics.publish',
            'users.manage', 'roles.manage', 'audit.view'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // Roles
        $roleSuperAdmin = \Spatie\Permission\Models\Role::create(['name' => 'super_admin']);
        // Super admin gets all permissions
        $roleSuperAdmin->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        $roleAdminPortal = \Spatie\Permission\Models\Role::create(['name' => 'admin_portal']);
        $roleAdminPortal->givePermissionTo([
            'pages.view', 'pages.create', 'pages.update', 'pages.publish',
            'posts.view', 'posts.create', 'posts.update', 'posts.publish',
            'events.manage', 'publications.manage', 'publications.publish',
            'services.view', 'services.manage',
            'facilities.view', 'facilities.manage', 'facilities.verify',
            'forum.manage',
            'aspirations.view', 'aspirations.manage', 'aspirations.publish',
            'statistics.view', 'statistics.manage', 'statistics.publish',
            'users.manage'
        ]);

        $roleContentEditor = \Spatie\Permission\Models\Role::create(['name' => 'content_editor']);
        $roleContentEditor->givePermissionTo([
            'pages.view', 'pages.create', 'pages.update',
            'posts.view', 'posts.create', 'posts.update',
            'events.manage', 'publications.manage'
        ]);

        $roleContentReviewer = \Spatie\Permission\Models\Role::create(['name' => 'content_reviewer']);
        $roleContentReviewer->givePermissionTo([
            'pages.view', 'pages.publish',
            'posts.view', 'posts.publish',
            'publications.publish'
        ]);

        $roleServiceManager = \Spatie\Permission\Models\Role::create(['name' => 'service_manager']);
        $roleServiceManager->givePermissionTo(['services.view', 'services.manage']);

        $roleFacilityManager = \Spatie\Permission\Models\Role::create(['name' => 'facility_manager']);
        $roleFacilityManager->givePermissionTo(['facilities.view', 'facilities.manage', 'facilities.verify']);

        $roleForumManager = \Spatie\Permission\Models\Role::create(['name' => 'forum_manager']);
        $roleForumManager->givePermissionTo(['forum.manage']);

        $roleAspirationOfficer = \Spatie\Permission\Models\Role::create(['name' => 'aspiration_officer']);
        $roleAspirationOfficer->givePermissionTo(['aspirations.view', 'aspirations.manage', 'aspirations.publish']);

        $roleComplaintOfficer = \Spatie\Permission\Models\Role::create(['name' => 'complaint_officer']);
        $roleComplaintOfficer->givePermissionTo([
            'complaints.view', 'complaints.assign', 'complaints.follow_up', 'complaints.close', 'complaints.export'
        ]);

        $roleLeader = \Spatie\Permission\Models\Role::create(['name' => 'leader']);
        $roleLeader->givePermissionTo(['statistics.view', 'statistics.manage', 'statistics.publish']);

        // Create default Super Admin user
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@portalkla.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');
    }
}
