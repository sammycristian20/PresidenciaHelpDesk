import { boolean } from 'helpers/extraLogics'

/**
 * This object will contains API endpints for fetching form-field json in edit scenario
 */
const FORM_FIELD_FETCH_APIS_FOR_EDIT = {
  ticket: 'api/agent/edit-mode-ticket-details/',
  user: '/user/edit/api/',
  organisation: '/organisation/edit/api/',
  recur: '/api/ticket/recur/'
}

/**
 * 
 * @param {String} category 
 * @param {Number} id 
 * 
 * Will pick API endpoint form FORM_FIELD_FETCH_APIS_FOR_EDIT on the basis of category and append the id to it
 * @returns {String} API endpoint
 */
export const getApiForFetchingFormFields = (category, id) => FORM_FIELD_FETCH_APIS_FOR_EDIT[category] + id


/**
 *
 * @param { String | Number | Array } value
 * @param { Array } options
 *
 * @param isDefault
 * @param type
 * @returns the form-field value compatible with the corresponding form-field-type component
 */
export const getFormFieldValue = (value, options, isDefault, type = '') => {

  if (type === 'file') return convertBase64ToFileObject(value);

  // If value is of type Array i.e., field type is multipleselect, we have to return all matching option-objects
  if (Array.isArray(value)) {

    if (isDefault) return value;
      
    else return options.filter(option => value.includes(option.label));

  // If options.length is 0 or options is falsy, return the value as it is.
  } else if (!boolean(options)) return (value ? value : '');

  // In rest of the case find the item from the options by its label and return the matched value
  return options.find((item) => item.label === value);
}

/**
 * 
 * @param {String} category 
 * 
 * @returns {String} unique
 * 
 * something unique for createing form instance
 * It's not a best way to get unique value, We can use some other logic or package for this
 */
export const getFormUniqueKey = (category = 'faveoform') => {
  return category + '__' + Date.now()
}


/**
 * 
 * @param {String} id DOM element ID 
 * 
 * This will find the ID in the DOM tree and scroll to that position
 */
export const scrollToErrorBlock = (id, className) => {
  if (!boolean(id)) return;

  setTimeout(() => {
    let elementById = document.getElementById(id)
    console.log('error elementById', elementById);
    if(elementById) {
      elementById.scrollIntoView({ behavior: 'smooth', block: 'end' })
    } else {

      // excluding selectors whose display is none
      let errorBlock = document.querySelectorAll('.error-block:not([style*="display: none"])')[0]
      console.log('error block', errorBlock);
      errorBlock && errorBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }, 1)
}


/**
 * 
 * @param {Object} reference vuejs $ref object
 * 
 * @returns {Promise}
 * 
 * Promise will get resolved by either TRUE or FALSE telling whether the form is valid or not
 */
export const validateFormData = async (reference) => {
  const { errors, isValid } = await reference.validateWithInfo();

  if (!isValid) {
    const firstInvalidField = Object.entries(errors).find((item) => item[1].length > 0);
    scrollToErrorBlock(firstInvalidField[0]);
    console.debug(firstInvalidField[0] + ' is not valid!');
  }

  return isValid;
}


/**
 * 
 * @param {Any} value 
 * @param {Boolean} isDefault 
 * @param {String} optionLabel 
 * 
 * This will return value which we have to send to backend for submitting the form
 */
export const getValueToSubmit = (value, isDefault, optionLabel = 'label') => {
  if (value === undefined) return;

  if (value instanceof Date || value instanceof File ) return value;

  else if (Array.isArray(value) && value[0] instanceof File) return value;

  else if (Array.isArray(value) && typeof value[0] !== 'object') return value;


  /*
     todo: should be removed once attachment module is rewritten
     NOTE FROM AVINASH: this is a jugaad to get servicedesk attachment to work. This must be removed once
     attachment module is rewritten. Kachra Ahead, be careful
     for servicedesk attachments, its possible that first item is an integer (attachment id) and rest is Files (fresh upload)
     so adding this jugaad to check if anything is a file object, return that
   */
  else if (Array.isArray(value)) return (value.map((val) => {
    if(val instanceof File){
      return val;
    }
    return isDefault ? val.id : val[optionLabel]
  }));

  // clean version. Should be uncommented once attachmemt module is rewritten
  // else if (Array.isArray(value)) return (value.map((val) => isDefault ? val.id : val[optionLabel]));

  else if (typeof value === 'object' && value !== null) return (isDefault ? value.id : value[optionLabel]);

  return value;
}

export const convertMapToSubmitableData = (map) => {

  let result = {};

  if (boolean(map) && map.size > 0) {
    for (let [key, value] of map) {

      if (Array.isArray(value) && value.length) {
        value.forEach((item, index) => {

          /*
             todo: should be removed once attachment module is rewritten
             NOTE FROM AVINASH: this is a jugaad to get servicedesk attachment to work. This must be removed once
             attachment module is rewritten. Kachra Ahead, be careful
             when unique is attachment
             servicedesk backend expect attachment_ids as id of the attachments but attachments in case of fresh attachment
             so putting check if attachment is a file object, we keep key as attachments else attachment_ids
           */
          // ============================== Kachra code ahead ===============================================
          if(key === 'attachments'){
            // not modifying original key to avoid unexpected behaviour
            let newKey = item instanceof File ? key : 'attachment_ids';
            result[`${newKey}[${index}]`] = item;
            return;
          }
          // ============================== Kachra code end =================================================

          result[`${key}[${index}]`] = item;
        })
      } else {
        result[key] = boolean(value) ? value : "";
      }
    }
  }

  return result;
}

function convertBase64ToFileObject(data) {

  if (!boolean(data)) return [];

  let fileArray = [];

  let files = Array.isArray(data) ? data : [data];

  for(let i = 0; i < files.length; i++) {

    // todo: should be removed once attachment module is rewritten
    // this is a jugaad written for servicedesk asset and organisation logo to work.
    // either rewrite and segregate the attachment module or do not touch it
    // Insha'allah boys played well
    if(files[i].id){
      fileArray.push(files[i]);
      continue;
    }

    if(files[i].hasOwnProperty('file') && files[i].hasOwnProperty('filename')) {
        let mime = files[i]['file'].match(/:(.*?);/)[1];
        fetch(files[i]['file']).then(res => res.blob()).then(blob => {
          fileArray.push(new File([blob], files[i]['filename'], {type: mime}));
      })
    }
  }
  console.log(fileArray);
  return fileArray;
}

