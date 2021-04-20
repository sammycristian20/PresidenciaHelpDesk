/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */

import {store} from "../../../store/store";
import {Validator} from 'easy-validator-js';
import {lang} from 'helpers/extraLogics';

/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateHolidaySettings(data){
    

    const { holiday_description, date} = data;

    //rules has to apply only after checking conditions
    let validatingData = {
       
        holiday_description: [holiday_description, 'isRequired'],
        date: [date, 'isRequired'],
    };
    
    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};

/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateBusinessHoursSettings(data){
    const { name,description,timezone,dateWithTime,open_time,close_time} = data;
    //rules has to apply only after checking conditions
    let validatingData = {
        name: [name, 'isRequired'],
        description: [description, 'isRequired'],
        timezone: [timezone, 'isRequired']
    };
    if(data.hours === 1){
         for (let i = 0; i < data.dateWithTime.length; i++) {
        if(dateWithTime[i].status === 'Open_custom'){
            let x = [];
            x[0] = dateWithTime[i].open_time;
            x[1] = 'isRequired'
            validatingData['open_time' + [i]] = x;
            let y = [];
            y[0] = dateWithTime[i].close_time;
            y[1] = 'isRequired'
            validatingData['close_time' + [i]] = y;
        }
    }
    }
   
       //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent
  
    return {errors, isValid};
};

