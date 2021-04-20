/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
this file will have function which would help to assign custom field label through the project
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

import {boolean, changeKeyNames} from 'helpers/extraLogics';
import {store} from 'store';
import $ from 'jquery';

/**
 * Variable are defind globally so that we can acces in both the function, also since $.each function
 * is being used as it would iterate over array as well an object easily
 */
let language = "";
let person = "";

export const assignLabel = (customfieldArray, lang, per, firstIteration = true) => {

  language = lang;
  person = per;
  $.each(customfieldArray, (index, value)=> { nestedAssignLabel(index, value, firstIteration)})
}

/**
 * Formats form field in whatever for it requires depending on the mode
 * It mutates form field array in th required form
 * @param  {string}  key
 * @param  {Object}  value  elements in form field array
 * @param  {Boolean} firstIteration  if the recursion is going on for the first time
 * @return {undefined}
 */
export const nestedAssignLabel = (key, value, firstIteration) => {
  if (typeof value === "object" && value != null) {
    if (
      value.type === "radio" ||
      value.type === "checkbox" ||
      (value.type === "select" && !boolean(value.default)) ||
      (value.type === "multiselect" && boolean(value.default))
    ) {
      let dafaultLabel = "";
      for (let i in value.options) {
        for (let j in value.options[i].labels) {
          if (value.options[i].labels[j].language == language) {
            value.options[i].labels = value.options[i].labels[j].label;
            break;
          } else {
            if (j == 0) {
              dafaultLabel = value.options[i].labels[j].label;
            }
            if (j == value.options[i].labels.length - 1) {
              value.options[i].labels = dafaultLabel;
            }
          }
        }
      }
    }

// ==========================================
    // whenever form is in workflow-listener mode, it should make first layer of forms as required and others, not rquired
    if(store.getters.getFormMode == 'workflow-listener'){
        handleWorkflowListenerMode(value, firstIteration);
    }
// =====================================


    //assign agent/user label
    let currentUserLabel = "";
    for (let a in value["labels_for_form_field"]) {

      if (value["labels_for_form_field"][a].language == language) {
        value["labels_for_form_field"] =
          value["labels_for_form_field"][a].label;
        break;
      } else {
        if (a == 0) {
          currentUserLabel = value["labels_for_form_field"][a].label;
        }
        if (a == value["labels_for_form_field"].length - 1) {
          value["labels_for_form_field"] = currentUserLabel;
        }
      }
    }
    //assign error message label
    let errorLabel = "";
    for (let a in value.labels_for_validation) {
      if (value.labels_for_validation[a].language == language) {
        value.labels_for_validation = value.labels_for_validation[a].label;
        break;
      } else {
        if (a == 0) {
          errorLabel = value.labels_for_validation[a].label;
        }
        if (a == value.labels_for_validation.length - 1) {
          value.labels_for_validation = errorLabel;
        }
      }
    }

    if(value.hasOwnProperty('title')){
      value.value = "";

      // attachment fields should not be there in edit and fork ticket because at the backend level, those attachments
      // gets merged into ticket thread, so not needed in edit or fork
      if(store.getters.getFormMode == 'edit-ticket' || store.getters.getFormMode == 'fork-ticket'){
        hideAttachments(value);
      }

      if(store.getters.getFormMode == 'recur-ticket'){
        handleRecurMode(value);
      }
    }

    assignUniqueKeyToFormFields(value);

    $.each(value, nestedAssignLabel, false)
  }
}


const hideAttachments = (formField) => {
  if(formField.type == 'file'){
    // making it none so that it doesnot gets displayed at all
    formField.display_for_agent = false;
    formField.display_for_user = false;
  }
}

/**
 * Updates values that is required in workflow-listener mode
 * @param  {Object} value
 * @param {Boolean} firstIteration  if the loop is getting called for the first time
 * @return {undefined}
 */
const handleWorkflowListenerMode = (formField, firstIteration) => {
  formField.display_for_agent = true;

  if(firstIteration){
    formField.required_for_agent = true;
  }else{
    formField.required_for_agent = false;
  }

  // in workflow listener mode validation is not required
  removeValidation(formField);
}


/**
 * manipulates form fields for recur mode, by removing attachment and add media option
 * @param  {Object} formField
 * @return {undefined}
 */
const handleRecurMode = (formField) => {
  formField.media_option = false;
  hideAttachments(formField);
}

/**
 * Removes validation from form field
 * @param  {object} formField
 * @return {undefined}
 */
const removeValidation = (formField) => {

    // removing all validation patterns because it is not supposed to be validated in workflow listener mode
    formField.pattern = null;

    // in workflow listener mode, there should be no validation other than required. So make it text
    if(formField.type == 'email'){
      formField.type = 'text';
    }
}
  /**
   * Assigns unique key to form field. for eg. Help Topic will have key help_topic_id
   * @param  {Object} value
   * @return {undefined}
   */
  const assignUniqueKeyToFormFields = (value) => {
    // if title is not present, terminate or unique key is already present
    if(!boolean(value.title) || value["unique"]){
      return;
    }

    // for custom field, key will be custom_id
    if (!boolean(value.default)) {
      value["unique"] = "custom_" + value.id;
    }
    else {
      switch (value.title) {
        case "Help Topic":
        case "Department":
        case "Status":
        case "Source":
        case "Location":
        case "Type":
        case "Priority":
        case "Assigned":

        // these 2 will be used in workflow
        case "Approval Workflow":
        case "Team":
          value["unique"] = changeKeyNames(value.title)+'_id';
          break;

        default:
          value["unique"] = changeKeyNames(value.title);
          break;
      }
    }
  }
