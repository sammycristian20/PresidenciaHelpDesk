<?php

/**
 * Contains variables which are global and could be used throughout the application
 * would be useful in case of activity log which can be appended to update all logs even before ticket is saved
 */

return [

    "log_categories" => [

        /**
         * temporarily stores ticket creation logs (before workflow gets executed)
         */
        "ticket_creation_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket updation logs(before listener gets executed)
         */
        "ticket_updation_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket changes during workflow execution
         */
        "ticket_workflow_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket changes during listener execution
         */
        "ticket_listener_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket changes during sla execution
         */
        "ticket_sla_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket updation logs(before listener gets executed)
         */
        "ticket_reply_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket updation logs(before listener gets executed)
         */
        "ticket_internal_note_logs"=>\Illuminate\Support\Collection::make(),

        /**
         * Temporarily stores ticket updation logs(before listener gets executed)
         */
        "ticket_forwarding_logs"=>\Illuminate\Support\Collection::make(),
    ],

    /**
     * A unique identifier by which it can be detected if multiple logs have same id (works like a batch)
     */
    "log_identifier" => null,
];
