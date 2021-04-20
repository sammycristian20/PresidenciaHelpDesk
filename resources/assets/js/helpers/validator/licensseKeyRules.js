import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateLicenseSettings(data){

		const { set1, set2, set3, set4 } = data;

		let validatingData = {
				
			set1: [set1,{'isRequired' :''}],

			set2: [set2,{'isRequired' :''}],

			set3: [set3,{'isRequired' :''}],

			set4: [set4,{'isRequired' :''}],
		};
		
		const validator = new Validator(lang);

		const {errors, isValid} = validator.validate(validatingData);

		store.dispatch('setValidationError', errors);
	
		return {errors, isValid};
};