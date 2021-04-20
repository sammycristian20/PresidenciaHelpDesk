<?php

Breadcrumbs::register('tasks.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(
        trans('Calendar::lang.task-plugin-project-and-category-settings'), route('tasks.settings')
    );
});
   
Breadcrumbs::register('task.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('Calendar::lang.tasks'), url('tasks/task?category=all'));
});

Breadcrumbs::register('task.create', function ($breadcrumbs) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push(Lang::get('Calendar::lang.create'), route('task.create'));
});

Breadcrumbs::register('calender.alltasks', function ($breadcrumbs) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push(Lang::get('Calendar::lang.calender_view'), route('calender.alltasks'));
});

Breadcrumbs::register('task.edit', function ($breadcrumbs, $taskId) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push(Lang::get('Calendar::lang.edit'), route('task.edit', $taskId));
});

Breadcrumbs::register('task.show', function ($breadcrumbs, $ticketId) {
    $breadcrumbs->parent('task.index');
    $breadcrumbs->push(Lang::get('Calendar::lang.task-plugin-view-task'), route('task.show', $ticketId));
});

Breadcrumbs::register('project.edit', function ($breadcrumbs, $projectID) {
    $breadcrumbs->parent('tasks.settings');
    $breadcrumbs->push(Lang::get('Calendar::lang.project_edit'), route('project.edit', $projectID));
});

Breadcrumbs::register('tasklist.edit', function ($breadcrumbs, $tasklistID) {
    $breadcrumbs->parent('tasks.settings');
    $breadcrumbs->push(Lang::get('Calendar::lang.tasklist_edit'), route('tasklist.edit', $tasklistID));
});

Breadcrumbs::register('tasks.template.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(
        trans('Calendar::lang.task-plugin-template-settings'), route('tasks.template.settings')
    );
});

Breadcrumbs::register('tasks.template.create', function ($breadcrumbs) {
    $breadcrumbs->parent('tasks.template.settings');
    $breadcrumbs->push(
        trans('Calendar::lang.task-plugin-task-template-create'), route('tasks.template.create')
    );
});

Breadcrumbs::register('tasks.template.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('tasks.template.settings');
    $breadcrumbs->push(
        trans('Calendar::lang.task-plugin-task-template-edit'), route('tasks.template.edit', $id)
    );
});
