/**
 * handles all system related settings
 * currently it is just storing allowed ticket actions for eg if a logged in user is allowed to change department or not
 */
import {errorHandler} from 'helpers/responseHandler';
import {store} from "store";
import axios from 'axios';

const state = {
    agentHeaderColor: '',
    adminHeaderColor: '',
    language : 'en',
    isRtl : false,
};

const getters = {

  getAgentHeaderColor(state){
    return state.agentHeaderColor;
  },

  getAdminHeaderColor(state){
    return state.adminHeaderColor;
  },

  getLanguage(state){
    return state.language;
  },

  getIsRtl(state){
    return state.isRtl;
  },
};

const mutations = {

  updateSystemSettings(state, payload){
    state.agentHeaderColor = payload.agent_header_color;
    state.adminHeaderColor = payload.admin_header_color;
    state.language = payload.language;
    state.isRtl = payload.is_rtl;
  }

};

const actions = {

  setSystemSettings({commit}){

    axios.get('api/system-settings').then(res =>{
      commit('updateSystemSettings', res.data.data);
    }
    ).catch(err => {
      errorHandler(err);
    })
  }
};

export default {state, getters, mutations, actions}
