import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function kbCommentValidation(data){

    const { name, email, website, comment } = data;
    
    //rules has to apply only after checking conditions
    let validatingData = {
        
        comment: [comment, 'isRequired', 'max(500)'],
    };

     if(Array.isArray(data.loginData.user_data)){
        
        validatingData['name'] = [name, 'isRequired', 'max(20)'];

        validatingData['email'] = [email, 'isRequired','isEmail'];
        
        validatingData['website'] =  [website, 'isUrl'];
    }
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};
