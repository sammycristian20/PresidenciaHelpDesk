/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */

import {validate} from "./validator";
import {store} from "store";


export function validateProfile(data){
	const {first_name} = data;

	const {errors, isValid} = validate({
		first_name: [first_name, 'isRequired'],
	});

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
}

