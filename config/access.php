<?php

/*
|--------------------------------------------------------------------------
| Dashboard access: permissions and default roles
|--------------------------------------------------------------------------
| Permissions are named "{area}.{action}", e.g. "projects.update".
| - The areas/actions below are created by the RolesAndPermissionsSeeder and
|   shown as checkboxes in Dashboard → Roles.
| - The "Super Admin" role skips every permission check (see AppServiceProvider)
|   and cannot be edited or deleted.
| (Not to be confused with config/permission.php, which configures the
|  spatie/laravel-permission package itself.)
*/

return [

    'super_admin' => 'Super Admin',

    // The first account, created by "php artisan db:seed" (AdminUserSeeder).
    // An empty password makes the seeder generate one and print it once.
    'first_admin' => [
        'name' => env('ADMIN_NAME', 'Rakiza Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@rakiza.test'),
        'password' => env('ADMIN_PASSWORD', ''),
    ],

    'areas' => [
        'projects' => ['label' => 'Projects', 'actions' => ['view', 'create', 'update', 'delete', 'publish']],
        'websites' => ['label' => 'Websites', 'actions' => ['view', 'create', 'update', 'delete', 'publish']],
        'services' => ['label' => 'Services', 'actions' => ['view', 'create', 'update', 'delete', 'publish']],
        'skills' => ['label' => 'Skills & stack', 'actions' => ['view', 'create', 'update', 'delete', 'publish']],
        'metrics' => ['label' => 'Numbers', 'actions' => ['view', 'create', 'update', 'delete', 'publish']],
        'messages' => ['label' => 'Messages', 'actions' => ['view', 'update', 'delete']],
        'settings' => ['label' => 'Settings', 'actions' => ['view', 'update']],
        'users' => ['label' => 'Users', 'actions' => ['view', 'create', 'update', 'delete']],
        'roles' => ['label' => 'Roles & permissions', 'actions' => ['view', 'create', 'update', 'delete']],
        'activity' => ['label' => 'Activity log', 'actions' => ['view']],
    ],

    'action_labels' => [
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Edit',
        'delete' => 'Delete',
        'publish' => 'Publish',
    ],

    // Roles created by the seeder ("area.*" = every action of that area).
    'default_roles' => [
        'Editor' => [
            'projects.*', 'websites.*', 'services.*', 'skills.*', 'metrics.*',
            'messages.*', 'activity.view',
        ],
        'Viewer' => [
            'projects.view', 'websites.view', 'services.view', 'skills.view',
            'metrics.view', 'messages.view',
        ],
    ],

];
