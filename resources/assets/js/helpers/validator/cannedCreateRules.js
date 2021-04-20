import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateCannedSettings (data){

    const {title, departments, share} = data;

    let validatingData = {

        title: [title, 'isRequired']
    };
    
    if(data.share){
       
      validatingData['departments'] = [data.departments,'isRequired'];
       
    }
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    store.dispatch('setValidationError', errors);
  
    return {errors, isValid};
};
