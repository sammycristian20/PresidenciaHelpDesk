/**
 * Store for new form builder impl
 */

const state = {

  formMenus: [], // form menu for drag element

  formFields: [], // array of individual element in form builder

  formCategoryType: 'ticket', // Category type, may be ticket, user, organisation etc

  isDisableDraging: false, // flag to enable/disable drag event, used to disable draging when modal popup is in open state

};

const getters = {

  getFormMenus(state) {
    return state.formMenus;
  },

  getFormFields(state) {
    return state.formFields;
  },

  getFormCategoryType(state) {
    return state.formCategoryType;
  },

  isDisableDraging(state) {
    return state.isDisableDraging;
  }


};

const mutations = {

  updateFormMenu(state, data) {
    state.formMenus = [...data];
  },

  updateFormFields(state, data) {
    state.formFields = [...data];
  },

  updateFormCategoryType(state, newType) {
    state.formCategoryType = newType;
  },

  updateIsDisableDraging(state, newVal) {
    state.isDisableDraging = newVal;
  }

};

const actions = {

  updateFormFields: ({ commit }, data) => {
    commit('updateFormFields', data);
  }

};


export default { state, getters, mutations, actions };