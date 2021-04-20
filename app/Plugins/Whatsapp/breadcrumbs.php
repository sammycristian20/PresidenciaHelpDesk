<?php
Breadcrumbs::register('whatsapp.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('Whatsapp::lang.whatsapp_settings'), route('whatsapp.settings'));
});

Breadcrumbs::register('whatsapp.edit', function ($breadcrumbs,$id) {
    $breadcrumbs->parent('whatsapp.settings');
    $breadcrumbs->push(Lang::get('Whatsapp::lang.whatsapp_edit_acc'), route('whatsapp.edit',$id));
});