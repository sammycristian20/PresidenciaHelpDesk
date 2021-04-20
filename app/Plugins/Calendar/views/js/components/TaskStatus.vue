<template>

    <div>
        <span class="label" :class="labelClass">{{ data.status }}</span>
        <span v-if="overdue" class="label label-danger">{{ lang('task-plugin-overdue') }}</span>
    </div>

</template>

<script>

    export default {
        name: 'task-status',
        props: {
            data: {
                type: Object,
                required: true
            }
        },
        computed: {
            labelClass() {
                switch (this.data.status) {
                    case "Open":
                        return 'label-success';
                    case "Closed":
                        return 'label-danger';
                    case "In-progress":
                        return 'label-warning';
                    default:
                        return 'label-default'
                }
            },
            overdue() {
                let today = new Date().toISOString();
                return today > this.data.task_end_date;
            },
        }
    }

</script>

<style>

</style>

