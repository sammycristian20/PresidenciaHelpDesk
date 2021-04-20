<?php

Route::group(['middleware' => ['web', 'auth', 'role.admin']], function () {

    // Event listener for adding view ot department create & end form
    \Event::listen('department-box-mounted', function ($action, $department = null) {
        echo (new \App\Plugins\DepartmentStatusLink\Controllers\DepartmentStatusLinkController)->{$action . 'FormField'}($department);
    });

    // Event listener for attaching statuses with department
    \Event::listen('saving-department', function ($department, $request) {
        (new \App\Plugins\DepartmentStatusLink\Controllers\DepartmentStatusLinkController)->attachStatusWithDepartment($department, $request);
    });

    // Event listener for attaching statuses with department
    \Event::listen('dependency-statuses-query-build', function (&$baseQuery, $supplements) {
        (new \App\Plugins\DepartmentStatusLink\Controllers\DepartmentStatusLinkController)->getDepartmentStatuses($baseQuery, $supplements);
    });
});
