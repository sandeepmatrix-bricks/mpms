<?php

/*
|--------------------------------------------------------------------------
| Module registry  (drives the Role permission screen)
|--------------------------------------------------------------------------
| Add a module on a single line here and it AUTOMATICALLY appears on the role
| screen with Read / Write / Edit / Delete checkboxes — no "Add Permission"
| step. Permission keys are generated as "{module}.{action}", e.g.
| "companies.read", "companies.delete".
|
|  read   = view / list           write  = create new
|  edit   = update existing       delete = remove
*/

return [

    // Super Admin area modules (gated for Sub Admins; Super Admin sees all).
    'platform' => [
        'companies' => 'Companies',
        'users' => 'Users',
        'sub_admins' => 'Sub Admins',
        'roles' => 'Roles',
    ],

    // Company area modules (gated per company Role; the company Admin sees all).
    // These drive the company Role screen's Read/Write/Edit/Delete grid.
    'company' => [
        'users' => 'Users',
        'roles' => 'Roles',
        'job_categories' => 'Job Departments',
        'jobs' => 'Job Management',
        'applicants' => 'Job Applicants',
        'statuses' => 'Application Statuses',
        'settings' => 'Settings',
    ],

    'actions' => ['read', 'write', 'edit', 'delete', 'deactivate'],
];
