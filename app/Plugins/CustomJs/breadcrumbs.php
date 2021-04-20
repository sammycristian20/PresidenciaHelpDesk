<?php
Breadcrumbs::register('customjs-settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('CustomJs::lang.custom-js'), route('customjs-settings'));
});

Breadcrumbs::register('customjs.delete', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('CustomJs::lang.custom-js'), route('customjs.delete'));
});

Breadcrumbs::register('customjs.create', function ($breadcrumbs) {
    $breadcrumbs->parent('customjs-settings');
    $breadcrumbs->push(Lang::get('lang.create'), route('customjs.create'));
});

Breadcrumbs::register('customjs.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('customjs-settings');
    $breadcrumbs->push(Lang::get('lang.edit'), url('custom-js/edit/{id}'));

});