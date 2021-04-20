/**
 * This Files contain validation specific to LdapSettings.vue only
 */
import { store } from 'store'
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateLdapSettingsSearchBase(data) {
  //rules has to apply only after checking conditions
  var validatingData = {
  };

  for (let i = 0; i < data.length; i++) {
    let x = [];
    x[0] = data[i].search_base;
    x[1] = 'isRequired'
    validatingData['searchbase' + [i]] = x;
  }


  //creating a validator instance and pasing lang method to it
  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  // write to vuex if errors
  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
