<template>
  <div class="link-ticket dropdown" v-if="ticketList && ticketList.length > 0">

    <i class="fa fa-ticket-alt fa-ticket link-ticket-btn" id="link-ticket-btn" aria-hidden="true" :title="lang('recent_tickets')" @click="openTicketList()"></i>

    <div id="ticket-list-dropdown" class="rt_dropdown-content">
        <div class="ticket-details-element" v-for="ticket in ticketList" :key="ticket.id" @click="onTicketClick(ticket)">
            <div class="ticket-details-element-table">
                <table>

                    <tr>
                        <td rowspan="3" :style="{ 'border-left': '5px solid ' + ticket.priority_color }" :title="'Priority: '+ ticket.priority"></td>
                        <td style="padding-top: 1rem; padding-left: 1rem;"><b>{{ticket.title}}</b></td>
                    </tr>

                    <tr>
                        <td style="padding-left: 1rem;">
                            <small>
                                <a class="rt-ticket-number" :href="basePath() + '/thread/' + ticket.id" target="_blank">#{{ticket.ticket_number}}</a>
                                <i :class="ticket.status_icon" :style="{'color': ticket.status_icon_color}" :title="'Status: ' + ticket.status"></i>
                            </small>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 1rem; padding-left: 1rem;">
                            <small>{{lang('created_at')}}: <b>{{formattedTime(ticket.created_at)}}</b></small>
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {

    props: {
        ticketList: { type: Array, default: () => [] },
        onTicketClick: { type: Function, required: true }
    },

    data: () => {
        return {}
    },


    mounted() {
        // Will close the dropdown if clicked outside
        window.onclick = function(event) {
            if (!event.target.matches('.link-ticket-btn')) {
                const dropdowns = document.getElementsByClassName("rt_dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    let openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('rt_show-list')) {
                        openDropdown.classList.remove('rt_show-list');
                    }
                }
            }
        }
    },

    computed: {
        ...mapGetters(['formattedTime']),
    },

    methods: {
        openTicketList() {
            document.getElementById("ticket-list-dropdown").classList.toggle("rt_show-list");
        },
    },

}
</script>

<style scoped>
    .rt_dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        bottom: 0;
        max-width: 450px;
        max-height: 60vh;
        min-width: 350px;
        overflow-y: auto;
        background-color: #FFFFFF;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    /* Links inside the dropdown */
    .rt_dropdown-content .ticket-details-element  {
        color: #333;
        text-decoration: none;
        display: block;
        border-bottom: 1px solid #dedede;
    }

    .ticket-details-element {
        cursor: pointer;
    }

    .ticket-details-element-table {
        padding-right: 1rem;
    }

    .rt-ticket-number {
        color: #b8c7ce;
    }

    .rt-ticket-number:hover {
        color: #3c8dbc;
    }
    .rt_show-list {
        display: block;
    }
    .link-ticket-btn {
        cursor: pointer;
    }
</style>