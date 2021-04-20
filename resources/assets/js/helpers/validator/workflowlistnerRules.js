/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */

import { store } from "store";
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */

export function validateWorkflowListner(data) {

  const target = data.obj.target;




  //rules has to apply only after checking conditions
  var validatingData = {
    target: [target, 'isRequired'],
  };

  for (let i = 0; i < data.actionList.length; i++) {
    let x = [];
    if (data.actionList[i].field === 'mail_agent') {
      x[0] = data.actionList[i].action_email.user_ids;
      x[1] = 'isRequired'
      validatingData['agent-' + [i]] = x;
      console.log(x, x[0], "x values")
    }

  }

  //creating a validator instance and pasing lang method to it
  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  // write to vuex if errors
  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };

}