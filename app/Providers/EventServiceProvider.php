<?php

namespace App\Providers;

use App\Listeners\Ticket\TicketActivityListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\WorkFlowEvent' => [
            'App\Listeners\WorkFlowListen',
        ],
        \Codedge\Updater\Events\UpdateAvailable::class => [
            \Codedge\Updater\Listeners\SendUpdateAvailableNotification::class
        ],
        \Codedge\Updater\Events\UpdateSucceeded::class => [
            \Codedge\Updater\Listeners\SendUpdateSucceededNotification::class
        ],
        \Codedge\Updater\Events\UpdateFailed::class => [
            \Codedge\Updater\Listeners\SendUpdateRequestNotification::class
        ],
        'App\Events\CustomOutboundEmail' => [
            'App\Listeners\CustomOutboundEmailListener',
        ],
        'App\Events\StatusChange' => [
            'App\Listeners\StatusChangeListen',
        ],

        'App\Events\WebHookEvent' => [
            'App\Listeners\WebHookListener',
        ],

        'App\Events\ReportExportEvent' => [
            'App\Listeners\ReportExportListener',
        ],

        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\CheckActiveAccount',
            'App\Listeners\RequiredVerifiedMail',
            'App\Listeners\LogSuccessfulLogin',

        ]
    ];

    /**
     * The subscriber classes to register.
     * @var array
     */
    protected $subscribe = [
        TicketActivityListener::class,
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // web notification event listener
        Event::listen('web-notify', function($data){
            $obj = (new App\Jobs\Notifications("browser-notification", $data));
            $obj->dispatch($obj);

        });

        // ticket details web hook listener
        Event::listen('ticket.details', function ($details) {
            $api_control = new \App\Http\Controllers\Common\ApiSettings();
            $api_control->ticketDetailEvent($details);
        });

        // post notofication saved event listener
        Event::listen('notification-saved', function($event) {
            $controller = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
            $controller->saved($event);
        });
    }
}
