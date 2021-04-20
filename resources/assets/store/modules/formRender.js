import { getValueToSubmit } from 'helpers/formUtils';

const RECUR_ADDITIONAL_INFO = {
  id: null,
  name: '',
  interval: '',
  delivery_on: '',
  start_date: '',
  end_date: '',
  execution_time: ''
}

/**
 * `formDataMap` => This will be a Map of key(unique) and value(selected value with any Data Type) 
 * `submitApiEndpoint` => SubmitEndpoint for each form insatnce
 */
class FaveoForm {
  constructor (scenario = 'create') {
    this.formDataMap = new Map();
    this.formDataMap.set('scenario' , scenario);
    this.submitApiEndpoint = '';
  }
}

const state = {

  isBatchTicketMode: false,

  recurAdditionalInfo: RECUR_ADDITIONAL_INFO,

  recaptchaKey: '',

  editorAttachments : [],

  faveoFormData: {},

  /**
   * Used for checking if child form inside a form is loading.
   * for eg. requester form in ticket form
   */
  childFormLoading: false,

  selectedReporter: 0,

  selectedValues: []

}

const getters = {

  getBatchTicketMode: (state) => state.isBatchTicketMode,

  getRecurAdditionalInfo: (state) => state.recurAdditionalInfo,

  getFaveoFormData: (state) => state.faveoFormData,

  getEditorAttachments: (state) => state.editorAttachments,

  getRecaptchaKey: (state) => state.recaptchaKey,

  getChildFormLoading: (state) => state.childFormLoading,

  getReporter: (state) => state.selectedReporter,

  getSelectedValues: (state) => state.selectedValues,

}

const mutations = {

  setSelectedValues: (state, value) => state.selectedValues = value,

  setBatchTicketMode: (state, value) => state.isBatchTicketMode = value,

  updateRecurProperties: (state, { key, value }) => state.recurAdditionalInfo[key] = value,

  setEditorAttachments: (state, value) => state.editorAttachments = value,

  setRecaptchaKey: (state, value) => state.recaptchaKey = value,

  setChildFormLoader: (state, value) => state.childFormLoading = value,

  // Will create new FaveoForm Instance
  createNewFormInstance: (state, {formUniqueKey, scenario}) => {
    state.faveoFormData[formUniqueKey] = new FaveoForm(scenario);
    state.recurAdditionalInfo = RECUR_ADDITIONAL_INFO;
    state.recaptchaKey = "";
    state.editorAttachments = [];
  },


  // Update faveo form instance object
  setFormData: (state, {id, formUniqueKey, isDefault, selectedValue, optionLabel}) => {

    // Update formValues
    const value = getValueToSubmit(selectedValue, isDefault, optionLabel);

    if( id == 'requester' ){
      state.selectedReporter = value;
    }

    if( id == 'asset_ids'){

      let arr = []
      for(let item in selectedValue ){
        arr.push( selectedValue[item] );
      }
      state.selectedValues = arr;
    }

    state.faveoFormData[formUniqueKey] && state.faveoFormData[formUniqueKey].formDataMap.set(id, value);

  },

  deleteFormDataByKey: (state, data) => {

    let _formData = state.faveoFormData[data.formUniqueKey];

    if (_formData) _formData.formDataMap.delete(data.key);

  },

  destroyFormInsatnce: (state, formUniqueKey) => delete state.faveoFormData[formUniqueKey],

  updateSubmitApiEndpoint: (state, { formUniqueKey, submitApiEndpoint }) => state.faveoFormData[formUniqueKey].submitApiEndpoint = submitApiEndpoint

}

const actions = {

  setSelectedValues: ( {commit}, value) => commit('setSelectedValues', value),

  setBatchTicketMode: ({ commit }, value) => commit('setBatchTicketMode', value),

  updateRecurProperties: ({ commit }, data) => commit('updateRecurProperties', data),

  setRecaptchaKey: ({ commit }, value) => commit('setRecaptchaKey', value),

  setEditorAttachments: ({ commit }, value) => commit('setEditorAttachments', value),

  setFormData: ({ commit }, data) => commit('setFormData', data),

  deleteFormDataByKey: ({ commit }, data) => commit('deleteFormDataByKey', data),

  createNewFormInstance: ({ commit }, data) => commit('createNewFormInstance', data),

  destroyFormInsatnce: ({ commit }, formUniqueKey) => commit('destroyFormInsatnce', formUniqueKey),

  updateSubmitApiEndpoint: ({ commit }, data) => commit('updateSubmitApiEndpoint', data),

  startChildFormLoader: ({ commit }) => commit('setChildFormLoader', true),

  stopChildFormLoader: ({ commit }) => commit('setChildFormLoader', false),
}

export default { state, getters, mutations, actions }

