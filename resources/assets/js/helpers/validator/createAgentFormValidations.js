/**
 * This file contains all the validation rules specific to form -- Create Update Agent.
 */

import { store } from "store";
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

export function validateCreateAgentFormFields(data){

    const { first_name, email, user_name, departments, agent_tzone, last_name, ext} = data;

    //rules has to apply only after checking conditions
    let validatingData = {
        email: [email, 'isRequired', 'isEmail'],
        first_name: [first_name, 'isRequired'],
        user_name: [user_name,'isRequired'],
        departments: [departments, 'isRequired'],
        agent_tzone: [agent_tzone, 'isRequired'],
        last_name: [last_name],
    };
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    if(errors.hasOwnProperty('user_name') && errors.user_name !== 'This field is required') {
      errors.user_name = 'Please type a valid user name';
    }

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};