<?php

namespace App\Listeners;

use App\Events\ReportExportEvent;
use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Settings\Email;
use Lang;

class ReportExportListener
{
    protected $notification;

    protected $mail;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notification = new NotificationController;

        $this->mail = new PhpMailController;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ReportExportEvent $event)
    {
        $this->sendBrowserNotification($event->report);

        $this->sendEmailNotification($event->report);
    }

    protected function sendBrowserNotification($report)
    {
        // Store new notification
        $this->notification->createNotification([
            'message' => Lang::get("report::lang.$report->type-name") . " Generated. Click here to download.",
            'to'      => $report->user->id,
            'by'      => $report->user->id,
            'table'   => 'reports',
            'row_id'  => $report->id,
            'url'     => route('report.export.download', $report->hash),
        ]);
    }

    protected function sendEmailNotification($report)
    {
        $report_link = route('report.export.download', $report->hash);

        // Send mail
        $this->mail->sendmail(
            Email::first()->sys_email,
            ['email' => $report->user->email],
            ['scenario' => 'report-export'],
            [
                'report_type'   => $report->type,
                'report_link'   => '<a href="' . $report_link . '" target="_blank">' . $report_link . '</a>',
                'report_expiry' => '<b>6 hours</b>',
            ]
        );
    }
}
