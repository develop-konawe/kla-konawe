<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions based on documentation
        $permissions = [
            'pages.view', 'pages.create', 'pages.update', 'pages.publish',
            'posts.view', 'posts.create', 'posts.update', 'posts.publish',
            'events.manage', 'publications.manage', 'publications.publish',
            'services.view', 'services.manage',
            'facilities.view', 'facilities.manage', 'facilities.verify',
            'forum.manage',
            'aspirations.view', 'aspirations.manage', 'aspirations.publish',
            'complaints.view', 'complaints.assign', 'complaints.follow_up', 'complaints.close', 'complaints.export',
            'statistics.view', 'statistics.manage', 'statistics.publish',
            'users.manage', 'roles.manage', 'audit.view'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Roles
        $roleSuperAdmin = Role::findOrCreate('super_admin');
        // Super admin gets all permissions
        $roleSuperAdmin->syncPermissions(Permission::all());

        $roleAdminPortal = Role::findOrCreate('admin_portal');
        $roleAdminPortal->syncPermissions([
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

        $roleContentEditor = Role::findOrCreate('content_editor');
        $roleContentEditor->syncPermissions([
            'pages.view', 'pages.create', 'pages.update',
            'posts.view', 'posts.create', 'posts.update',
            'events.manage', 'publications.manage'
        ]);

        $roleContentReviewer = Role::findOrCreate('content_reviewer');
        $roleContentReviewer->syncPermissions([
            'pages.view', 'pages.publish',
            'posts.view', 'posts.publish',
            'publications.publish'
        ]);

        $roleServiceManager = Role::findOrCreate('service_manager');
        $roleServiceManager->syncPermissions(['services.view', 'services.manage']);

        $roleFacilityManager = Role::findOrCreate('facility_manager');
        $roleFacilityManager->syncPermissions(['facilities.view', 'facilities.manage', 'facilities.verify']);

        $roleForumManager = Role::findOrCreate('forum_manager');
        $roleForumManager->syncPermissions(['forum.manage']);

        $roleAspirationOfficer = Role::findOrCreate('aspiration_officer');
        $roleAspirationOfficer->syncPermissions(['aspirations.view', 'aspirations.manage', 'aspirations.publish']);

        $roleComplaintOfficer = Role::findOrCreate('complaint_officer');
        $roleComplaintOfficer->syncPermissions([
            'complaints.view', 'complaints.assign', 'complaints.follow_up', 'complaints.close', 'complaints.export'
        ]);

        $roleLeader = Role::findOrCreate('leader');
        $roleLeader->syncPermissions(['statistics.view', 'statistics.manage', 'statistics.publish']);

        // Create or update default Super Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@portalkla.local'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['super_admin']);
    }
}
