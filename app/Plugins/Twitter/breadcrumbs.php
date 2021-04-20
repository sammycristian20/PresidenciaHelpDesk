<?php
Breadcrumbs::register('twitter.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('Twitter::lang.twitter-integration'), route('twitter.settings'));
});

