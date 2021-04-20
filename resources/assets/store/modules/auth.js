/**
 * handles logged-in user's data in-and-out of vuex store.
 * As soon as action 'updateUser' is called, It checks if state.user is already populated. If not, 
 *       it sends a request to backend to retrieve logged-in user's data.
 * If success response is recieved, it stores data to vuex sends the response to errorHandler
 */

import moment from 'moment';
import momentTimezone from 'moment-timezone';
import {errorHandler} from './../../js/helpers/responseHandler';
import {preciseTimeLeft, humanReadableTimeApprox} from './../../js/helpers/extraLogics'

const state = {
    user: '',//when use is logged out, user will be empty object{}
    userLoadStatus: 0,
};

const getters = {
    /**
     * checks if 'user' state is empty, if not then checks if it has a valid timezone, 
     * if finds a valid timestamp it returns that else retuns UTC as default timezone
     * @param {object|string} state         current state of vuex auth variable (or this.state.auth)
     * @return {string}                     timezone string
     */
     //get user details
    getUserData(state){
        
        return state.user;
    },

    /*
      Returns the user load status.
    */
    getUserLoadStatus( state ){
      
      return state.user !== '';
    },

    timezone(state) {
        let timezoneAtServer = sessionStorage.getItem('timezone');
        if(!timezoneAtServer){
            return moment.tz.guess();
        }
        return timezoneAtServer;
    },

    /**
     * checks if 'user' state is empty, if not then checks if it has a valid date format, 
     * if finds a valid format it returns that else retuns UTC as default timezone
     * @param {object|string} state         current state of vuex auth variable (or this.state.auth)
     * @return {string}                     date-format string
     */
    timeFormat(state) {

        const format = sessionStorage.getItem('data_time_format');

        if(format === 'human-read'){
            return 'Y-MM-dd hh:mm:ss';
        }

        return convertCarbonToMoment(format);
    },

    /**
     * formats time indate format
     * @return {string}                     date string without time
     */
    dateFormat(state, getters) {
        let format = sessionStorage.getItem('date_format')
        return convertCarbonToMoment(format);
    },


    /**
     * gets time difference between passed time and current time 
     * USAGE: simply pass an argument 'time'
     * 
     * @param  {string} time        time string passed in UTC in default format from backend
     * @return {string}             time difference between curent time and passed time if difference is positive else 0
     */
    timeDiffForFuture(state){

        return function(time){
            
            return preciseTimeLeft(time);
        }
    },

    /**
     * gets time difference between passed time and current time 
     * USAGE: simply pass an argument 'time'
     * 
     * @param  {string} time        time string passed in UTC in default format from backend
     * @return {string}             time difference between curent time and passed time
     */
    timeDiffForPast(state){

        return function(time){
         
            //this method is located in helpers/extraLogics
            return humanReadableTimeApprox(time);
        }
    },



    /**
     * gets formatted time in timezone & timeFormat stored in vuex store
     * @param  {string} time        time string passed in UTC in default format from backend
     * @return {string}             time sting in timezone and time-format stored in vuex
     */
    formattedTime(state, getters){
        return function(time, format = "LLL"){
            if(!time){ //if time is null
                return '--';
            }
            return moment.utc(time).tz(getters.timezone).format(getters.timeFormat);
        }
    },

    customFormattedTime(state, getters){
        return function(time, format = "LLL"){
            if(!time){ //if time is null
                return '--';
            }
            return moment.utc(time).tz(getters.timezone).format(format);
        }
    },


    /**
     * gets formatted date in timezone & timeFormat stored in vuex store
     * @param  {string} time        time string passed in UTC in default format from backend
     * @return {string}             time sting in timezone and time-format stored in vuex
     */
    formattedDate(state, getters){
        return function(time){

            if(!time){ //if time is null
                return '---';
            }

            //using browser timezone directly
            return moment.utc(time).tz(moment.tz.guess()).format(getters.dateFormat);
        }
    },
};

const mutations = {

    /**
     * updates user state
     * @param {object} state    current state of vuex auth variable. (or this.state.auth)
     * @param {object} user     data of logged in user
     * return {void}
     */
    getUserDataFromBackend(state, user) {
        state.user = user;
    },


    /*
      Sets the user load status
    */
    setUserLoadStatus( state, status ){
      state.userLoadStatus = status;
    },
};

const actions = {

    /**
     * checks if state.user is empty. If it is, it sends an get request to backend to recieve user data and pass
     * it to getUserDataFromBackend method.
     * @param {object} {commit}
     * @return {void}
     */
    updateUser({commit}){
        
        if (this.state.auth.user === '') {

            axios.get('/api/get-auth-info').then(res => {
                
                commit('getUserDataFromBackend', res.data.data);

            }).catch(err => {
                
                commit('getUserDataFromBackend', '');
                
                errorHandler(err);
            });
        }
    },

    updateUserData({commit},data){
        
        commit('getUserDataFromBackend',data);
    },
    
    deleteUser({commit}) {
        commit('getUserDataFromBackend', '');
    }
};

/**
 * Backend uses carbon for time conversion and carbon represents minute as i and m as month
 * frontend uses moment and moment represents m for minute and M for month.
 * @param format
 * @returns {*}
 */
function convertCarbonToMoment(format){
    if(!format){
        return 'LLL';
    }

    return format.replace('m','MM').replace('d','DD').replace('i','mm').replace('s','ss').replace('H','HH')
        .replace('F','MMMM').replace('g','hh').replace('j','DD').replace('S','');
}

export default {state, getters, mutations, actions}