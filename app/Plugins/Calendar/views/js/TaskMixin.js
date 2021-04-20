export const TaskMixin = {
    data() {

        return {
            task_name        : null,
            required         : true,
            task_description : null,
            associated_ticket: undefined,
            assignee         : [],
            task_start_date  : null,
            task_end_date    : null,
            due_alert        : null,
            alert_repeat     : null,
            task_category_id     : null,
            is_private       : 0,
            taskId           : null,
            task             : '',
            mode             : null,
            loading          : false,
            timeOptions: {start: '00:00', step: '00:30',  end: '23:30'},
            numberStyle : { width : '20%' },
            elementsTaskAlert: [
                {
                    name: "On day of event",
                    value: "event_day",
                },
                {
                    name: "5 minutes before",
                    value:"5_minutes_before",
                },
                {
                    name: "15 minutes before",
                    value:"15_minutes_before",
                },
                {
                    name: "30 minutes before",
                    value:"30_minutes_before",
                },
                {
                    name: "1 hour before",
                    value:"1_hour_before",
                },
                {
                    name: "2 hours before",
                    value:"2_hours_before",
                },
                {
                    name: "1 day before",
                    value:"1_day_before"
                },
                {
                    name: "No Reminder",
                    value:"no_reminder",
                },
            ],
            elementsAlertRepeat: [
                {
                    name: "Never",
                    value: "never",
                },
                {
                    name: "Daily",
                    value: "daily",
                 },
                {
                    name: "Weekly",
                    value: "weekly",
                 },
                {
                    name: "Monthly",
                    value: "monthly",
                },

            ],
            elementTaskStatus: [
                {
                    name: 'Open',
                    value: 'Open'
                },
                {
                    name: 'Closed',
                    value: 'Closed',
                },
                {
                    name:'In-progress',
                    value: 'In-progress'
                }
            ],

            status: {
                name: 'Open',
                value: 'Open'
            },

        }

    },
}