/**
 * Formbuilder json
 * It contains all the from fields
 */


const state = {

  /**
   * form data for ticket, user and organization
   * WARNING: this field has been used for storing `user`, `ticket` and `organisation` fields with wrong naming convention
   * @type {Array}
   */
  ticketForm: [],

  /**
   * If batch ticket is enabled
   * @type {Boolean}
   */
  batchTicketEnabled : false,

  /**
   * Stores ticket form field
   * @type {Array}
   */
  ticketFormFields: [],

  /**
   * Stores user form fields
   * @type {Array}
   */
  userFormFields: [],

  /**
   * Stores organisation form fields
   * @type {Array}
   */
  organisationFormFields: [],

  /**
   * FormMode can be `create-ticket` or `workflow-listner` or `recur-ticket` or `edit-ticket` or fork-ticket
   * @type {String}
   */
  formMode: 'create-ticket',

  /**
   * Captcha key
   * @type {String}
   */
  captchaKey: "",

  /**
   * total size allowed for a request in kb
   * @type {Number}
   */
  post_max_size: 0,

  /**
   * max allowed upload size for single file in kb
   * @type {Number}
   */
  upload_max_filesize: 0,

  /**
   * total number of file uploads that are allowed
   * @type {Number}
   */
  max_file_uploads: 0,

  /**
   * Dependency object which contains all the dependencies required by Listener events
   * NOTE: this has to be removed once helptopic and department child field concept is corrected
   * REASON : current code is written in a way that it accept All dependency in one
   * only in case of events in listeners. It will require `additional` jugaad to make it
   * work with existing components because child fields of helptopic and department should
   * not come there. Plus, `Any` option should come in events(this one can be done though).
   *
   * Though it is inefficient approach but keeping the code debuggable is the priority
   * at this point.
   * @type {Object}
   */
  dependency : {},
};

const getters = {
	//get ticket form
	getTicketForm(state) {
		return state.ticketForm;
	},

  //get linkModule
	getBatchTicketEnabled(state) {

    // NOTE: batch ticket should only be available in create mode.
    // in edit, fork, recur, workflow-listener, it should be disabled
    if(state.formMode == 'create-ticket'){
      return state.batchTicketEnabled;
    }
    return false;
	},

  /**
   * gets ticket form field clone without, so that if it is mutated, it will have no affect on original getter
   */
  getTicketFormFields(state){
    return state.ticketFormFields;
  },

  /**
   * gets user form field clone without, so that if it is mutated, it will have no affect on original getter
   */
  getUserFormFields(state){
    return state.userFormFields;
  },

  /**
   * gets organisation form field clone without, so that if it is mutated, it will have no affect on original getter
   */
  getOrganisationFormFields(state){
    return state.organisationFormFields;
  },

  getFormMode(state){
    return state.formMode;
  },

  getCaptchaKey(state){
    if(state.captchaKey == ""){
      return "wrong_captcha_key";
    }
    return state.captchaKey;
  },

  getPostMaxSize(state){
    return Math.round(state.post_max_size/1000);
  },

  getUploadMaxFilesize(state){
    if(state.post_max_size < state.upload_max_filesize){
      return Math.round(state.post_max_size/1000);
    }
    return Math.round(state.upload_max_filesize/1000);
  },

  getMaxFileUploads(state){
    return state.max_file_uploads;
  },

  /**
   * gets dependency from dependency object by accepting dependency parameter
   * REASON : current code is written in a way that it accept All dependency in one
   * only in case of events in listeners. It will require `additional` jugaad to make it
   * work with existing components because child fields of helptopic and department should
   * not come there. Plus, `Any` option should come in events(this one can be done though).
   *
   * Though it is inefficient approach but keeping the code debuggable is the priority
   * at this point.
   */
  getDependency(state){
    return function(dependency){
      let dependencyKey = dependency.replace('-', '_')
      return state.dependency[dependencyKey];
    }
  },
};

const mutations = {
	//update ticket form
	UpdateTicketForm(state, payload) {
		state.ticketForm = payload;
	},

  //update linking module
	updateBatchTicketEnabled(state, payload) {
		state.batchTicketEnabled = payload;
	},

  updateTicketFormFields(state, payload) {
  	state.ticketFormFields = payload;
  },

  updateUserFormFields(state, payload) {
    state.userFormFields = payload;
  },

  updateOrganisationFormFields(state, payload) {
    state.organisationFormFields = payload;
  },

  updateFormMode(state, payload) {
    state.formMode = payload;
  },

  updateCaptchaKey(state, payload) {
    state.captchaKey = payload;
  },

  updateMetaFormData(state, payload) {
    state.captchaKey = payload.captcha_key;
    state.post_max_size = payload.post_max_size;
    state.upload_max_filesize = payload.upload_max_filesize;
    state.max_file_uploads = payload.max_file_uploads;
  },

  /**
   * Adds dependency into dependency object
   * NOTE : this has to be removed once helptopic/department child field concept is corrected
   * REASON : current code is written in a way that it accept All dependency in one
   * only in case of events in listeners. It will require `additional` jugaad to make it
   * work with existing components because child fields of helptopic and department should
   * not come there. Plus, `Any` option should come in events(this one can be done though).
   *
   * Though it is inefficient approach but keeping the code debuggable is the priority
   * at this point.
   */
  appendDependency(state, {key, value}){
    state.dependency[key] = value;
  },
};

const actions = {
	//intialize ticket form value
	initTicketFormBuilder({ commit }, option) {
		console.log(option, "OPTIONS")
		axios.get('api/get-form-data?category=' + option).then((resp) => {
			commit('UpdateTicketForm', resp.data.data.form_fields);
		}).catch(function (resp) {
			// console.log(resp.response.data);
		})
	},
	//set ticket form value
	setTicketForm({ commit }, ticket_form) {
		commit('UpdateTicketForm', ticket_form)
	},

  setBatchTicketEnabled({commit}, payload){
    commit('updateBatchTicketEnabled', payload);
  },

  setTicketFormFields({commit}, payload){
    commit('updateTicketFormFields', payload)
  },

  setUserFormFields({commit}, payload){
    commit('updateUserFormFields', payload)
  },

  setOrganisationFormFields({commit}, payload){
    commit('updateOrganisationFormFields', payload)
  },

  setFormMode({commit}, payload){
    commit('updateFormMode', payload)
  },

  setCaptchaKey({commit}, payload){
    commit('updateCaptchaKey', payload)
  },

  setMetaFormData({commit}, payload){
    commit('updateMetaFormData', payload);
  },

  /**
   * Sets dependency into dependency object by creating a new key without discarding the old keys
   * REASON : current code is written in a way that it accept All dependency in one
   * only in case of events in listeners. It will require `additional` jugaad to make it
   * work with existing components because child fields of helptopic and department should
   * not come there. Plus, `Any` option should come in events(this one can be done though).
   *
   * Though it is inefficient approach but keeping the code debuggable is the priority
   * at this point.
   */
  setDependency({commit}, {key, value}){
    commit('appendDependency', {key, value});
  },
}

export default { state, getters, mutations, actions }
