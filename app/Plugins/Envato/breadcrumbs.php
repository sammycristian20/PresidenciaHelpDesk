<?php
Breadcrumbs::register('envanto-settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push('Envato', route('envanto-settings'));
});