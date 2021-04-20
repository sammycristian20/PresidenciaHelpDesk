import {store} from "store";
import {Validator} from 'easy-validator-js';
import {lang} from 'helpers/extraLogics';

export function validateApprovalWorkflow(data){
  const {name, levels} = data;

  //levels has to be validated based on index and class name
  let validatingData = {
    name : [name, 'isRequired']
  }

  let isValid, errors;

  levels.map((level,index) => {
    validatingData['level-name-'+index] = [level.name, 'isRequired'];
    validatingData['level-match-'+index] = [level.match, 'isRequired'];
    isValid = validateUsersAndUserTypes(level.approvers.users, level.approvers.user_types, index)
  })

  const validator = new Validator(lang);

  const validatorData = validator.validate(validatingData);
  errors = validatorData.errors;
  isValid = !isValid ? isValid : validatorData.isValid;

  // write to vuex if errors
  store.dispatch('setValidationError', errors);

  return {errors, isValid};
}

/**
 * Validates user and user_type fields. Either one of the field must be present
 * @return {undefined}
 */
function validateUsersAndUserTypes(users, userTypes, index){
  let missingScenarios = 0;

  //either of the fields has to be selected
  if(users.length == 0 && userTypes.length == 0){
      let message = lang('atleast_one_user_or_one_user_type_is_required');
       missingScenarios++;
      //dispatch an action saying that one of the above field is required
      store.dispatch('setAlert', { type: 'danger', message: message, component_name: 'approval-level-'+index })
  }

  if(missingScenarios > 0){
    return false;
  }

  return true;
}
