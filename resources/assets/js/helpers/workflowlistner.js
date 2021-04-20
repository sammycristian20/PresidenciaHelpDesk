
import { getValueFromNestedArray } from 'helpers/extraLogics';

/**
 * setWorkFlowValue is used to set the rule/actions key inside the object,
 * when you receive the raw array of object to structure it in proper way required in backend
 * @param {Array} data
 * @param {Array} editvalues
 * @param {Object} workflowlist
 * @param {String} type
 * @param {Number} index
 */
export const setWorkFlowValue = (data, editvalues, workflowlist, type, index) => {
  data.map(res => {
    // if fieldField unique matches workflow field, assign the value to workflowList
    if (res.unique == workflowlist.field) {
      // if formField array already has a value
      if ( res.value) {
        workflowlist.value = res.value;

      } else {
        // case of editValues
        if (shallBindEditValue(editvalues, index, res)) {
          res.value = editvalues[index].value;
        }

        //TODO: currently I am not sure how this crap is working. It needs to be refactored
        else if(res.value == ""){
          workflowlist.value = res.value;
        }
      }
    }

    if (res.unique) {
      if (res.options != undefined && res.options.length > 0) {

        workflowlist[type] = _.compact(
          res.options.map(option => {
            if (res.value == option.value) {
              if (option.nodes.length > 0) {
                return {
                  field: option.labels ? option.labels : option.name,
                  value: option.value != undefined ? option.value : res.value,
                  node: option.nodes,
                  editdata:
                    editvalues.length > 0
                      ? editvalues
                      : null
                };
              }
            } else {
              return {
                field: option.labels ? option.labels : option.name,
                value: option.value != undefined ? option.value : res.value,
                node: option.nodes ? option.nodes : [],
                editdata:
                  editvalues.length > 0
                    ? editvalues
                    : null
              }
            }
          })
        );
      }
      if (
        workflowlist.id != null &&
        workflowlist != undefined &&
        Boolean(workflowlist[type][0])
      ) {

        data.map(data => {
          if (data.options.length > 0) {
            data.options.map(value => {
              if (value.nodes.length > 0) {
                appendEditData(value.nodes, editvalues[index]);
              }
            });
          }
        });
      }
    }
  });
}

/**
 * appendEditData helps us append the edit value to the custom fields array which can be so that values are,
 * visible to the user
 * @param {Array} customdata
 * @param {Array} editvalues
 */
export const appendEditData = (customdata, editvalues) => {

  if (customdata.length) {
    customdata.map(res => {
      if (res.options.length > 0) {
        res["value"] = getValueFromNestedArray(
          editvalues,
          res.unique,
          "value"
        );
        appendOptionValues(res.options, editvalues);
      } else {
        res["value"] = getValueFromNestedArray(
          editvalues,
          res.unique,
          "value"
        );
      }
    });
  }
}

/**
 * appendOptionValues method helps to append value inside the options values of the custom array fields,
 * if the value is not fetched then it would be assign to null
 * @param {Array} data
 * @param {Array} editvalues
 */
export const appendOptionValues = (data, editvalues) => {
  data.map(x => {
    if (x.nodes.length > 0) {
      x.nodes.map(y => {
        if (y.options.length > 0) {
          y.value = getValueFromNestedArray(
            editvalues,
            y.unique,
            "value"
          );
          appendEditData(x.nodes, editvalues);
        } else {
          y.value = getValueFromNestedArray(
            editvalues,
            y.unique,
            "value"
          );
        }
      });
    }
  });
}

/**
 * Shall bind value of form fields with editValues or not
 * @param  {Array} editValues array of edited values in action/rule
 * @param  {Number} index      index of the action/rule
 * @param  {Object} formField  field with which value has to be binded
 * @return {Boolean}
 */
export const shallBindEditValue = (editValues, index, formField) => {
  return (editValues != undefined && editValues[index] != undefined
    && formField.unique == editValues[index].field);
}
