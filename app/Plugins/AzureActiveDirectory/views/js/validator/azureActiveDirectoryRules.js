import { store } from 'store'
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateAzureSettings(data)
{

    //rules has to apply only after checking conditions
    const { appName, appId, appSecret, tenantId } = data

    var validatingData = {
        appName: [appName, 'isRequired'],
        appId: [appId, 'isRequired'],
        appSecret: [appSecret, 'isRequired'],
        tenantId: [tenantId, 'isRequired'],
    };

    //creating a validator instance and pasing lang method to it
    const validator = new Validator(lang);

    const { errors, isValid } = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors);

    return { errors, isValid };
}
