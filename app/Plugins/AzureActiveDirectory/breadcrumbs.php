<?php

Breadcrumbs::register('azure-active-directory.index', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('AzureActiveDirectory::lang.azure_configuration_settings'), route('azure-active-directory.index'));
});

Breadcrumbs::register('azure-active-directory.create', function ($breadcrumbs) {
    $breadcrumbs->parent('azure-active-directory.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('azure-active-directory.create'));
});

Breadcrumbs::register('azure-active-directory.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('azure-active-directory.index');
    $breadcrumbs->push(Lang::get('lang.edit'), route('azure-active-directory.edit', $id));
});

