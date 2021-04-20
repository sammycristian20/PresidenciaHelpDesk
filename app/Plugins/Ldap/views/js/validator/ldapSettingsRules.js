/**
 * This Files contain validation specific to LdapSettings.vue only
 */
import {store} from 'store'
import { Validator } from 'easy-validator-js';
import { lang } from 'helpers/extraLogics';

/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */

export function validateLdapSettings(data) {
  // console.log(data.search_bases.length, "inside ldap rule setting")
  const { domain, username, password, ldap_label, forgot_password_link } = data



  //rules has to apply only after checking conditions
  var validatingData = {
    domain: [domain, 'isRequired'],
    username: [username, 'isRequired'],
    password: [password, 'isRequired'],
    ldap_label: [ldap_label, 'max(24)'],
    // forgot_password_link:[forgot_password_link, 'isUrl'],
  };

  //creating a validator instance and pasing lang method to it
  const validator = new Validator(lang);

  const { errors, isValid } = validator.validate(validatingData);

  // write to vuex if errors
  store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors, isValid };
}
