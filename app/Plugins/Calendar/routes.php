<?php

    \Event::listen('agent-panel-navigation-data-dispatch', function (&$navigationContainer) {
        $task = new App\Plugins\Calendar\Controllers\TaskController();
        $task->injectTaskAgentNavigation($navigationContainer);
    });

    \Event::listen('admin-panel-navigation-data-dispatch', function (&$navigationContainer) {
        (new App\Plugins\Calendar\Controllers\CalendarAdminNavigationController)
          ->injectCalendarAdminNavigation($navigationContainer);
    });

    \Event::listen('agent-panel-scripts-dispatch', function () {
        echo "<script src=".bundleLink('js/calendar.js')."></script>";
    });

    \Event::listen('admin-panel-scripts-dispatch', function () {
        echo "<script src=".bundleLink('js/calendar.js')."></script>";
    });

    \Event::listen('workflow-enforced', function ($ticket, $values) {

        $taskTemplateId = checkArray('task_list_id', $values);

        if ($taskTemplateId) {
            (new \App\Plugins\Calendar\Handler\TaskWorkFlowHandler())
                ->handle($taskTemplateId, ['ticket_id' => $ticket->id, 'assigned_to' => $ticket->assigned_to]);
        }

    });

    \Event::listen('enforcer-dependency-detail-dispatch', function ($field, &$dependencyValue) {
        (new \App\Plugins\Calendar\Handler\TaskWorkFlowHandler())->setTaskListEntityValueForWorkflow($field, $dependencyValue);
    });

    \Event::listen('ticket-automator-form-dispatch', function (&$formFields) {
        (new \App\Plugins\Calendar\Handler\TaskWorkFlowHandler())->addTaskForm($formFields);
    });

    Route::group(['prefix' => 'tasks/api','middleware' => ['web']], function () {
        /////////////////////////////////////////Project///////////////////////////////////////////////////////
        Route::post('project/create', 'App\Plugins\Calendar\Controllers\ProjectController@store');

        Route::get('project/view', 'App\Plugins\Calendar\Controllers\ProjectController@index');

        Route::delete('project/delete/{projectId}', 'App\Plugins\Calendar\Controllers\ProjectController@destroy');

        Route::put('project/edit/{projectId}', 'App\Plugins\Calendar\Controllers\ProjectController@edit');

        Route::get('projects', 'App\Plugins\Calendar\Controllers\ProjectController@returnProjects');

        /////////////////////////////////////////TaskList/////////////////////////////////////////////////
        Route::post('category/create', 'App\Plugins\Calendar\Controllers\TaskCategoryController@store');

        Route::get('category/view', 'App\Plugins\Calendar\Controllers\TaskCategoryController@index');

        Route::delete('category/delete/{categoryId}', 'App\Plugins\Calendar\Controllers\TaskCategoryController@destroy');

        Route::put('category/edit/{categoryId}', 'App\Plugins\Calendar\Controllers\TaskCategoryController@edit');

        Route::get('categories', 'App\Plugins\Calendar\Controllers\TaskCategoryController@returnTaskLists');

    });

    Route::group(['prefix' => 'tasks','middleware' => ['web', 'role.agent']], function () {

    //////////////////////////////////////////Task/////////////////////////////////////////////////////////////
        Route::get('api/get-all-tasks', 'App\Plugins\Calendar\Controllers\TaskController@returnTasks');

        Route::resource('task', 'App\Plugins\Calendar\Controllers\TaskController');

        Route::get('api/get-task-by-id/{id}', 'App\Plugins\Calendar\Controllers\TaskController@getTaskById');

        Route::get('api/change-task/{id}/{status}', 'App\Plugins\Calendar\Controllers\TaskController@changeStatus');

        Route::get('api/list', 'App\Plugins\Calendar\Controllers\TaskController@getListOfTasks');

        Route::get('api/get-all-ticket-tasks', 'App\Plugins\Calendar\Controllers\TaskController@getAllTicketTasks');

        //For Calendar View
        Route::get('api/get-all-tasks-for-calendar', 'App\Plugins\Calendar\Controllers\TaskController@getTasks');

        Route::get('api/activity/{taskId}', 'App\Plugins\Calendar\Controllers\TaskActivityController@show');

        Route::name('tasks.template.apply')->post('api/template/apply')
            ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@applyTemplate');

        Route::name('tasks.template.dropdown')->get('api/template/dropdown')
            ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@dropDownList');

        /////////////////////////////////////////View///////////////////////////////////////////////////
        Route::get('/', 'App\Plugins\Calendar\Controllers\TaskController@getCalendarTaskPage')
            ->name('calender.alltasks');


        Route::get('project/edit/{projectID}', 'App\Plugins\Calendar\Controllers\ProjectController@editForm')
            ->name('project.edit');

        Route::get('category/edit/{categoryID}', 'App\Plugins\Calendar\Controllers\TaskListController@editForm')
            ->name('category.edit');
    });

    Route::group(['prefix' => 'tasks', "middleware" => ['web','role.admin']], function () {

        Route::get('settings', 'App\Plugins\Calendar\Controllers\TaskController@viewWithTaskSettings')
            ->name('tasks.settings');

        Route::name('tasks.template.settings')->get('template/settings')
            ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@settings');

        Route::name('tasks.template.create')->get('template/create')
           ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@create');

        Route::name('tasks.template.store')->post('api/template/store')
           ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@store');

        Route::name('tasks.template.update')->put('api/template/update/{id}')
            ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@update');

        Route::name('tasks.template.index')->get('api/template/index')
           ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@index');

        Route::name('tasks.template.edit')->get('template/edit/{id}')
            ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@edit');

        Route::name('tasks.template.delete')->delete('api/template/delete/{id}')
            ->uses('App\Plugins\Calendar\Controllers\TaskTemplateController@destroy');

    });
