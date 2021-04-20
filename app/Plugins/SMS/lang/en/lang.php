<?php

return [
    /*************breadcrumbs*****************/
    'SMS'                => 'SMS',
    'providers'          => 'SMS Providers',
    'send-options'       => 'Sending Options',
    'template-sets'	     => 'Template Sets',
    'set'	             => 'Set',
    'scheduler'          => 'Scheduler',
    'open-ticket'        => 'Open Ticket Notifications',
    'scheduled-messages' => 'Scheduled Messages',
    'add-message'        => 'Add New Message',

    /***************main page****************/
    'provider-settings'  => 'Provider Settings',
    'msg-sending-option' => 'Message Sending Options',
    'sms-templates'      => 'SMS Templates',
    'sms-scheduler'      => 'SMS Shceduler',

    /**************providers page **********/
    'sms-providers'                         => 'SMS Provider',
    'select-provider'                       => 'Select provider',
    'auth-key'                              => 'Auth Key',
    'sender-id'                             => 'Sender Id',
    'auth-key-placeholder'                  => 'Enter you msg91\'s API key',
    'sender-id-placeholder'                 => 'Enter alphanumeric string of 6 characters.',
    'route'                                 => 'Route',
    'route-placeholder'                     => 'Eneter route to send messages eg. default or 1',
    'email'                                 => 'Email',
    'email-placeholder'                     => 'Email of owner',
    'sub-account'                           => 'Sub account',
    'sub-account-plcaeholder'               => 'Enter name of sub account',
    'subacc-password'                       => 'Password',
    'subacc-pswd-placeholder'               => 'Enter sub account\'s password',
    'sender'                                => 'Sender',
    'sender-placeholder'                    => 'Eneter sender Id to show message receivers',
    'mobile'                                => 'Mobile',
    'mobile-placeholder'                    => 'Eneter mobile number to receive test message',
    'provider-settings-saved-successfully'  => 'Provider settings saved successfully.',
    'can-not-connect-to-service-provider'   =>'Can not connect to service provder.',

    /********************sms sending options************************/
    'sms-sending-option'                 => 'SMS Sending Options',
    'send-sms-clients'                   => 'Send SMS to clients',
    'ticket-creation-acknoledgement'     => 'Acknowledgment of a ticket creation',
    'close-resolve-notification'         => 'Notfication when ticket gets closed or resolved',
    'ticket-assignment-information'      => 'When ticket gets assigned to an agent',
    'send-sms-to-agents'                 => 'Send SMS to Agents',
    'notification-of-ticket-creation'    => 'Notification on creation of a new ticket',
    'notification-of-ticket-assignment'  => 'Notification of ticket assignment',
    'sending-options-saved-successfully' => 'Settings for message sending options saved successfully.',

    /************************sms templates************************/
    'create_template'                     => 'Create Template',
    'template'                            => 'template',
    'activate_this_set'                   => 'Activate this set',
    'template-set-name'                   => 'Template Set Name:',
    'template-set-name-placeholder'       => 'Enter name of the template',
    'create'                              => 'Create',
    'template-not-found'                  => 'Template not found.',
    'template-set-not-found'              => 'Template set not found.',
    'sms-template-created-successfully'   => 'New tempalte set has been created. You can edit it and activate.',
    'template-set-deleted-successfully'   => 'Selected templates have been deleted Successfully. Please note you can not delete System default template or an active template set.',
    'template-set-deletion-error'         => 'Could not delete the selected templates. Please make sure you are not deleting an active template set or system default template set.',
    'template-set-activated-successfully' => 'Template set has been activated.',
    'available-variables'                 => 'Available variables',
    'template-updated-successfully'       => 'Template has been updated succuessfully',

    /************** some common words **************************/
    'name'                => 'Name',
    'status'              => 'Status',
    'action'              => 'Action',
    'show'                => 'Show',
    'inactive'            => 'Inactive',
    'active'              => 'Active',
    'type'                => 'Type',
    'description'         => 'Description',
    'edit'                => 'Edit',
    'tips'                => 'Tips: Copy variable name as it is to use the variable in your template.',
    'update'              => 'Update',
    'content'             => 'Content',
    'content-placeholder' => 'Enter your message. You can also use above listed variables in your message template.',
    'tip-tooltip'         => 'Click here to see list of available variables',
    'select-template'     => 'Please select template sets to delete.',

    /*******************scheduler *****************************/
    'sms-scheduler-setting'                   => 'Scheduled SMS settings',
    'open-ticket-notification-settings'       => 'Open Ticket Notifications Settings',
    'scheduled-custom-message'                => 'Custom Scheduled Messages',
    'send-to-clients'                         => 'Send to clients',
    'send-to-clients-tooltip'                 => 'Send Notification messages to clients for their tickets which are open.',
    'send-to-agents'                          => 'Send to agents',
    'send-to-agents-tooltip'                  => 'Send Notification messages to agents for open tickets.',
    'send-after-minutes-tooltip'              => 'Enter time span(in minutes) after which notification message should be sent to clients and agents if a ticekt is open. This time span will be calculated from the time when the ticket was created. Minimum time span is 30 minutes.',
    'send-after-minutes'                      => 'Send notifications after',
    'send_after_minutes_placeholder'          => 'in minutes',
    'assigned_only-tooltip'                   => 'Select \'Yes\' if you want agents to recieve notification only about the tickets which are assigned to them otherwise we\'ll send them notifications about all open tickets in their respective departments.',
    'assigned_only'                           => 'Agents notification for assigned tickets only',
    'cron-status-tooltip'                     => 'Decide how frequently system should run cron for sending scheduled messages.',
    'cron-status'                             => 'SMS Automation',
    'run-auto-cron'                           => 'run auto cron',
    'select'                                  => 'Select',
    'every-minute'                            => 'Every Minute',
    'every-five-minute'                       => 'Every Five Minute',
    'every-ten-minute'                        => 'Every Ten Minute',
    'every-thirty-minute'                     => 'Every Thirty Minute',
    'every-hour'                              => 'Every Hour',
    'every-day'                               => 'Every Day',
    'every-week'                              => 'Every Week',
    'monthly'                                 => 'Monthly',
    'yearly'                                  => 'Yearly',
    'notification-setting-saved'              => 'Notification seetings updated successfully.',
    'notification-settings-error'             => 'Could not update the settings please try after some time.',
    'select-cron-time'                        => 'Select cron running time.',
    'send-after-required'                     => '"Send notification after" is a required field.',
    'custom-messages'                         => 'Custom Scheduled Messages',
    'add-message'                             => 'Add message',
    'send-at'                                 => 'Send on (dd/mm/yy)',
    'message-not-found'                       => 'Message not found',
    'edit-schedule-message'                   => 'Edit scheduled message',
    'add-schedule-message'                    => 'Add scheduled message',
    'send-scheduled-messages-to-clients'      => 'Send scheduled message to clients',
    'send-scheduled-messages-to-agents'       => 'Send scheduled message to agents',
    'send-on'                                 => 'Send on',
    'message-scheduled-successfully'          => 'Your message has been shceduled.',
    'message-scheduled-failed'                => 'Your message could not be scheduled, please try again.',
    'scheduled-messages-deleted-successfully' => 'Selected scheduled messages are deleted successfully.',
    'scheduled-messages-not-deleted'          => 'We could not delete the seelcted message, please try after some time.',
    'scheduled-messages-updated-successfully' => 'Scheduled message has been updated successfully.',
    'scheduled-messages-not-updated'          => 'We could not update the message, please try again later.',
    'select-message'                          => 'Please select messages to delete.',

];