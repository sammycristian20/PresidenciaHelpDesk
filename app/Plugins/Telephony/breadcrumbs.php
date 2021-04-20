<?php
Breadcrumbs::register('telephone.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('Telephony::lang.telephone-integration'), route('telephone.settings'));
});
Breadcrumbs::register('telephone.provider.setting', function ($breadcrumbs) {
    $breadcrumbs->parent('telephone.settings');
    $breadcrumbs->push(Lang::get('Telephony::lang.provider'), route('telephone.provider.setting',['short'=>'']));
});

