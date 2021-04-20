<?php
Breadcrumbs::register('lime-survey.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('LimeSurvey::lang.lime-survey-settings'), route('lime-survey.settings'));
});

?>