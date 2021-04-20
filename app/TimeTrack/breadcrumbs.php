<?php

Breadcrumbs::register('timetrack.setting', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('timetrack::lang.time-track'), route('timetrack.setting'));
});
