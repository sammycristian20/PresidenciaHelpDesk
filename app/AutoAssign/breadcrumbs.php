<?php


Breadcrumbs::register('auto.assign', function($breadcrumbs)
{
    $breadcrumbs->parent('setting');
    $breadcrumbs->push('Auto-Assign', route('auto.assign'));
});

    