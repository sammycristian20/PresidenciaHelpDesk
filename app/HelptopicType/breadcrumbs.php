<?php

Breadcrumbs::register('helpdesk.helptopic-type.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('HelptopicType::lang.helptopic_type'),route('helpdesk.micro.organizarion'));
});
