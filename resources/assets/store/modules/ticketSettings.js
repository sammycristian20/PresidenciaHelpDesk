/**
 * handles all tickets settings related data.
 * currently it is just storing allowed ticket actions for eg if a logged in user is allowed to change department or not
 */
import { errorHandler } from 'helpers/responseHandler';
import { store } from "../store";
import axios from 'axios';
import {boolean} from 'helpers/extraLogics';

const state = {
    ticket_actions: '',
    selected_ticket_id: '',
    assigned_user_id: '',
    tickets_per_page: '',
    table_bar_color: '#3c8dbc', //by default this color
    selected_category: 'Inbox',
    reply_time_track: '',
    time_track_status: '',
    selected_form_values: {
        help_topic: '',
        department: '',
    },
    ticket_id : 0,
};

const getters = {

    //gets actions list with allowed actions
    getTicketActions(state){
        if(state.ticket_actions !== ''){
            return state.ticket_actions
        }
        return false;
    },

    ticketActions(state){
        // if(state.ticket_actions !== ''){
            //this method is defined in the same file
            return state.ticket_actions;
        // }
        // return false;
    },
    //get selected ticket id
    getSeletedTicketId(state) {
        return state.selected_ticket_id;
    },

    getTicketsPerPage(state) {
        return state.tickets_per_page;
    },

    getTimetrackReplySettings(state) {
        return state.reply_time_track;
    },

    getTimetrackStatus(state) {
        return state.time_track_status;
    },

    getApprovalWorkflowProgessVisibility(state) {
        if (state.ticket_actions.view_approval_progress) {
            return true;
        }
        return false;
    },

    getEnforceApprovalWorkflowVisibility(state) {
        if (state.ticket_actions.allowed_enforce_approval_workflow) {
            return true;
        }
        return false;
    },

    getApprovalActionVisibility(state) {
        if (state.ticket_actions.allowed_approval_action) {
            return true;
        }
        return false;
    },

    //color of ticket table bar
    getTableBarColor(state) {
        return state.table_bar_color;
    },

    getLinkedFormValues(state) {
        return state.selected_form_values;
    },

    getSelected_nodeObject(state) {
        return state.selected_nodeObject;
    },

    getStoredTicketId(state){
        return state.ticket_id;
    },

};

const mutations = {

    //updates user state
    updateTicketsActionData(state, payload) {
        state.ticket_actions = payload;
        state.time_track_status = payload.time_track_enabled;
        state.reply_time_track = payload.show_thread_worktime;
    },

    //update ticket id
    updateTicketId(state, payload) {
        state.selected_ticket_id = payload;
    },

    //update ticket id
    updateId(state, payload){
        state.ticket_id = payload;
    },

    //updates ticket settings data
    ticketSettings(state, payload) {
        state.tickets_per_page = payload.ticket_per_page;
        state.table_bar_color = payload.table_bar_color;
    },

    assignedUserId(state, payload) {
        state.assigned_user_id = payload;
    },

    assignSelectedDepartment(state, payload){
      state.selected_form_values.department = payload;
    },

    assignSelectedHelptopic(state, payload){
      state.selected_form_values.help_topic = payload;
    },
};

const actions = {

    /**
     * checks if ticketSettings.ticket_actions is empty. If it is, it sends an get request to backend to recieve
     * USAGE : should be used only when ticketTimeline gets mounted
     * @param {object} {commit}
     * @return {void}
     */
    updateTicketActions({ commit }, ticketId) {
        if(ticketId){
          axios.get('/api/agent/action-list', { params: { ticket_id: ticketId } })
              .then(res => {
                  commit('updateTicketsActionData', res.data.data.actions);
              })
              .catch(err => {
                  commit('updateTicketsActionData', '');
                  errorHandler(err);
              });
        }

    },

    //remove seletd ticket id
    setTicketIdEmpty({ commit }, ticketId) {
        commit('updateTicketId', ticketId);
    },

    //set seletd ticket id
    setTicketId({commit},ticketId){
        commit('updateId',ticketId);
    },

    updateTicketSettings({commit}){
        if (this.state.ticketSettings.tickets_per_page === '') {
            axios.get('/api/ticket/settings')
                .then(res => {
                    commit('ticketSettings', res.data.data);
                })
                .catch(err => {
                    commit('ticketSettings', '');
                    errorHandler(err);
                });
        }
    },

    //stores the info of the person to whom ticket is assigned
    setAssignedUserId({ commit }, payload) {
      commit('assignedUserId', payload);
    },

    setSelectedDepartment({commit}, payload) {
      let payloadId = "";
      if(boolean(payload)){
        payloadId = payload.id;
      }
      commit('assignSelectedDepartment', payloadId);
    },

    setSelectedHelptopic({commit}, payload) {
      let payloadId = "";
      if(boolean(payload)){
        payloadId = payload.id;
      }
      commit('assignSelectedHelptopic', payloadId);
    },

    resetLinkingObject({commit}){
      commit('assignSelectedHelptopic', '');
      commit('assignSelectedDepartment', '');
    }
};

export default { state, getters, mutations, actions }
