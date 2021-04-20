import {store} from "store";

import {Validator} from 'easy-validator-js';

import {lang} from 'helpers/extraLogics';

export function validateReminderSettings(data){

  const { approach_response, approach_resolution, violated_response, violated_resolution } = data;

  let validatingData = {}

  let isValid, errors;

  if(approach_response.length > 0){

    approach_response.map((field,index) => {

      validatingData[field.type+'-count-'+index] = [field.reminder_delta, 'isRequired'];
    
      validatingData[field.type+'-option-'+index] = [field.option, 'isRequired'];
    
      isValid = validateAgentsAndAgentTypes(field.reminder_receivers.agents, field.reminder_receivers.agent_types, index)
    })  
  }

  if(approach_resolution.length > 0){

    approach_resolution.map((field,index) => {

      validatingData[field.type+'-count-'+index] = [field.reminder_delta, 'isRequired'];
    
      validatingData[field.type+'-option-'+index] = [field.option, 'isRequired'];
    
      isValid = validateAgentsAndAgentTypes(field.reminder_receivers.agents, field.reminder_receivers.agent_types, index)
    })  
  }

  if(violated_response.length > 0){

    violated_response.map((field,index) => {

      validatingData[field.type+'-count-'+index] = [field.reminder_delta, 'isRequired'];
    
      validatingData[field.type+'-option-'+index] = [field.option, 'isRequired'];
    
      isValid = validateAgentsAndAgentTypes(field.reminder_receivers.agents, field.reminder_receivers.agent_types, index)
    })  
  }

  if(violated_resolution.length > 0){

    violated_resolution.map((field,index) => {

      validatingData[field.type+'-count-'+index] = [field.reminder_delta, 'isRequired'];
    
      validatingData[field.type+'-option-'+index] = [field.option, 'isRequired'];
    
      isValid = validateAgentsAndAgentTypes(field.reminder_receivers.agents, field.reminder_receivers.agent_types, index)
    })  
  }
  
  const validator = new Validator(lang);

  const validatorData = validator.validate(validatingData);
  
  errors = validatorData.errors;
  
  isValid = !isValid ? isValid : validatorData.isValid;

  store.dispatch('setValidationError', errors);

  return {errors, isValid};
}

/**
 * Validates agent and agent_types fields. Either one of the field must be present
 * @return {undefined}
 */
function validateAgentsAndAgentTypes(agents, agentTypes, index){

  let missingScenarios = 0;

  //either of the fields has to be selected
  if(agents.length == 0 && agentTypes.length == 0){

      let message = lang('atleast_one_agent_or_one_agent_type_is_required');
       missingScenarios++;
    
      //dispatch an action saying that one of the above field is required
      store.dispatch('setAlert', { type: 'danger', message: message, component_name: 'reminder' })
  }

  if(missingScenarios > 0){
    return false;
  }

  return true;
}
