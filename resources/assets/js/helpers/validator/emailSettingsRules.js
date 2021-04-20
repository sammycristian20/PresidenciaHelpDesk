/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */

import {store} from "store";
import {Validator} from 'easy-validator-js';
import {lang} from 'helpers/extraLogics';

/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
export function validateEmailSettings(data){


    const { email_address, email_name, password, fetching_protocol, fetching_host,fetching_status, fetching_encryption,
        fetching_port, sending_protocol, sending_status, sending_host, sending_port, sending_encryption,
        api_key, secret, domain, region, version} = data;

    //rules has to apply only after checking conditions
    let validatingData = {
        email_address: [email_address, 'isRequired', 'isEmail'],
        email_name: [email_name, 'isRequired', 'isAlphaNumeric(false)', 'max(50)', 'min(2)'],
    };

    //if fetching status is ON
    (fetching_status == 1) && (validatingData.fetching_protocol = [fetching_protocol, 'isRequired']);

    //if sending status is ON
    (sending_status == 1) && (validatingData.sending_protocol = [sending_protocol, 'isRequired']);

    //if password field is not visible, there should be no validation on that
    (shallPasswordBeVisble(data)) && (validatingData.password = [password, 'isRequired', 'min(3)', 'max(255)']);

    shallFetchingHostBeVisible(data) && (validatingData.fetching_host = [fetching_host, 'isRequired']);

    shallFetchingPortBeVisible(data) && (validatingData.fetching_port = [fetching_port, 'isRequired']);

    shallFetchingEncryptionBeVisible(data) && (validatingData.fetching_encryption = [fetching_encryption, 'isRequired']);

    //if host field is not visible,
    //there should be no validation on that
    shallSendingHostBeVisible(data) && (validatingData.sending_host = [sending_host, 'isRequired']);

    shallSendingPortBeVisible(data) && (validatingData.sending_port = [sending_port, 'isRequired']);

    shallSendingEncryptionBeVisible(data) && (validatingData.sending_encryption = [sending_encryption, 'isRequired']);

    //if apiKey is visible, it will be required
    (shallApiKeyBeVisible(data)) && (validatingData.api_key = [api_key, 'isRequired']);

    //if secret is visible, it will be required
    (shallSecretBeVisible(data)) && (validatingData.secret = [secret, 'isRequired']);

    //if domain is visible, it will be required
    (shallDomainBeVisible(data)) && (validatingData.domain = [domain, 'isRequired']);

    //if region is visible, it will be required
    (shallRegionBeVisible(data)) && (validatingData.region = [region, 'isRequired']);

    // if version is visible, it will be required
    (shallVersionBeVisible(data)) && (validatingData.version = [version, 'isRequired']);

    const validator = new Validator(lang);

    const {errors, isValid} = validator.validate(validatingData);

    // write to vuex if errors
    store.dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return {errors, isValid};
};

/**
 * computes if password should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallPasswordBeVisble(data) {

    const { fetching_status, sending_protocol, sending_status } = data;

    if((sending_status == 1 && (sending_protocol=== 'smtp' || sending_protocol=== 'ews')) || fetching_status == 1){
        return true;
    }
    return false;
}

/**
 * computes if fetching host should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallFetchingHostBeVisible(data) {
    const {fetching_status} = data
    if(fetching_status == 1){
        return true;
    }
    return false;
}


/**
 * computes if fetching port should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallFetchingPortBeVisible(data) {
    if(shallFetchingHostBeVisible(data)){
      const {fetching_protocol} = data;
      return fetching_protocol !== 'ews';
    }
    return false;
}


/**
 * computes if fetching encryption should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallFetchingEncryptionBeVisible(data) {
    if(shallFetchingHostBeVisible(data)){
      const {fetching_protocol} = data;
      return fetching_protocol !== 'ews';
    }
    return false;
}

/**
 * computes if sending host should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallSendingHostBeVisible(data) {
    const {sending_status, sending_protocol} = data
    if(sending_status == 1 && (sending_protocol == 'smtp' || sending_protocol == 'ews')){
        return true;
    }
    return false;
}

/**
 * computes if fetching port should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallSendingPortBeVisible(data) {
    if(shallSendingHostBeVisible(data)){
      const {sending_protocol} = data;
      return sending_protocol !== 'ews';
    }
    return false;
}


/**
 * computes if fetching encryption should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallSendingEncryptionBeVisible(data) {
    if(shallSendingHostBeVisible(data)){
      const {sending_protocol} = data;
      return sending_protocol !== 'ews';
    }
    return false;
}

/**
 * computes if apiKey should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallApiKeyBeVisible(data) {
    const { sending_protocol } = data;
    if(sending_protocol === 'ses'){
        return true;
    }
    return false;
}

/**
 * computes if secret should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallSecretBeVisible(data) {
    const { sending_protocol } = data;
    if(sending_protocol === 'mailgun' || sending_protocol === 'mandrill'
        || sending_protocol === 'sparkpost' || sending_protocol === 'ses'){
        return true;
    }
    return false;
}

/**
 * computes if domain should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallDomainBeVisible(data) {
    const { sending_protocol } = data;
    if(sending_protocol === 'mailgun'){
        return true;
    }
    return false;
}

/**
 * computes if region should be visible
 * @param {String} data      state data of emailSettings component
 * @return {Boolean}         true if visible else false
 * */
export function shallRegionBeVisible(data) {
    const { sending_protocol } = data;
    if(sending_protocol === 'ses'){
        return true;
    }
    return false;
}

/**
 * computes if exchange web service version is required in fetching
 * @param  {Object} data  state data of emailSettings component
 * @return {Boolean}
 */
export function shallVersionBeVisible(data) {
  const { version, fetching_protocol, sending_protocol } = data;
  // if sending protocol is ews or if fetching protocol is ews, it should be required
  if(fetching_protocol == 'ews' || sending_protocol == 'ews'){
    return true;
  }
  return false;
}
