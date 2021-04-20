<?php
Breadcrumbs::register('facebook.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('Facebook::lang.facebook_integration_settings'), route('facebook.settings'));
});

Breadcrumbs::register('facebook.security.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('facebook.settings');
    $breadcrumbs->push(Lang::get('Facebook::lang.facebook_app_settings'), route('facebook.security.settings'));
});

Breadcrumbs::register('facebook.integration.edit', function ($breadcrumbs,$id) {
    $breadcrumbs->parent('facebook.settings');
    $breadcrumbs->push(Lang::get('Facebook::lang.facebook_page_edit'), route('facebook.integration.edit',$id));
});

Breadcrumbs::register('facebook.integration.create', function ($breadcrumbs) {
    $breadcrumbs->parent('facebook.settings');
    $breadcrumbs->push(Lang::get('Facebook::lang.facebook_page_create'), route('facebook.integration.create'));
});