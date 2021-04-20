<?php
Breadcrumbs::register('chat.post', function($breadcrumbs)
{
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(trans('chat::lang.chat'), route('chat.post'));
});
Breadcrumbs::register('chat.settings', function($breadcrumbs)
{
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(trans('chat::lang.chat_settings'), route('chat.settings'));
});

//chat.edit
Breadcrumbs::register('chat.edit', function($breadcrumbs,$id)
{
    $breadcrumbs->parent('chat.settings');
    $breadcrumbs->push(trans('chat::lang.edit_chat'), route('chat.edit',$id));
});