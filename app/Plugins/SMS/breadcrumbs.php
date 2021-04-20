<?php
Breadcrumbs::register('sms-settings', function ($breadcrumbs) {
    $breadcrumbs->parent('plugins');
    $breadcrumbs->push(Lang::get('SMS::lang.SMS'), route('sms-settings'));
});
Breadcrumbs::register('providers-settings', function ($breadcrumbs) {
    $breadcrumbs->parent('sms-settings');
    $breadcrumbs->push(Lang::get('SMS::lang.providers'), route('providers-settings'));
});
Breadcrumbs::register('message-sending-options', function ($breadcrumbs) {
    $breadcrumbs->parent('sms-settings');
    $breadcrumbs->push(Lang::get('SMS::lang.send-options'), route('message-sending-options'));
});
Breadcrumbs::register('sms-template-sets', function ($breadcrumbs) {
    $breadcrumbs->parent('sms-settings');
    $breadcrumbs->push(Lang::get('SMS::lang.template-sets'), route('sms-template-sets'));
});
Breadcrumbs::register('show-set', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('sms-template-sets');
    $template = App\Plugins\SMS\Model\TemplateType::select('set_id')->where('id', '=', $id)->first();
    $template_set = App\Plugins\SMS\Model\TemplateSets::select('name')->where('id', '=', $template->set_id)->first();
    $breadcrumbs->push($template_set->name, url('sms/show-template-set').'/'.$id);
});
Breadcrumbs::register('edit-template', function ($breadcrumbs, $id) {
    $template_name = App\Plugins\SMS\Model\TemplateType::select('type', 'set_id')->where('id', '=', $id)->first();
    $breadcrumbs->parent('show-set', $template_name->set_id);
    $breadcrumbs->push($template_name->type, route('edit-template', $id));
});
Breadcrumbs::register('sms-scheduler', function ($breadcrumbs) {
    $breadcrumbs->parent('sms-settings');
    dump($breadcrumbs->parent('sms-settings'));
    $breadcrumbs->push(Lang::get('SMS::lang.scheduler'), route('sms-scheduler'));
});
Breadcrumbs::register('open-ticket-notifications', function ($breadcrumbs) {
    $breadcrumbs->parent('sms-scheduler');
    $breadcrumbs->push(Lang::get('SMS::lang.open-ticket'), route('open-ticket-notifications'));
});
Breadcrumbs::register('custom-schedule-message', function ($breadcrumbs) {
    $breadcrumbs->parent('sms-scheduler');
    $breadcrumbs->push(Lang::get('SMS::lang.scheduled-messages'), route('custom-schedule-message'));
});
Breadcrumbs::register('add-schedule-messaage', function ($breadcrumbs) {
    $breadcrumbs->parent('custom-schedule-message');
    $breadcrumbs->push(Lang::get('SMS::lang.add-message'), route('add-schedule-messaage'));
});
Breadcrumbs::register('show-message', function ($breadcrumbs, $id) {
    $message = App\Plugins\SMS\Model\Scheduler\ScheduleMsg::select('name')->where('id', '=', $id)->first();
    $breadcrumbs->parent('custom-schedule-message');
    $breadcrumbs->push('Edit &nbsp; > &nbsp;'.$message->name, url('sms/scheduled-message/edit/{id}'));
});
