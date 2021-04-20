import {store} from "store";
import {Validator} from 'easy-validator-js';
import {lang} from 'helpers/extraLogics';

/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateCustomColumn(data){

    const { name, equation } = data;

    //rules has to apply only after checking conditions
    let validatingData = {
        name: [name, 'isRequired', 'max(60)'],
        equation: [equation, 'isRequired', 'max(200)'],
    };

    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return {errors, isValid};
}
