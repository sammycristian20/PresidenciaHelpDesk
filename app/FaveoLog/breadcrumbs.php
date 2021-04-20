<?php

Breadcrumbs::register('logs', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(__('log::lang.logs'), route('logs'));
});

Breadcrumbs::register('system.logs', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(__('lang.system-logs'), route('system.logs'));
});