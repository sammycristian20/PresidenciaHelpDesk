/**
 * Common utilty for report
 */

'use strict';

import { boolean } from 'helpers/extraLogics';


/**
 * returns filter array as key value pair
 * e.g; [{key: created_at, value: last::13~day}]
 * @param {Object} filterParams 
 */
export const getFilterObjectToArray = (filterParams = {}) => {
  let filterArray = [];
    for (const key in filterParams) {
      if (filterParams.hasOwnProperty(key) && isValidFilterFieldValue(filterParams[key])) {
        let filterObj = {};
        filterObj.key = key;
        filterObj.value = filterParams[key];
        filterArray.push(filterObj);
      }
    }
  return filterArray;
}


/**
 * returns only filter objects
 * @param {Object} filterParams 
 */
export const getValidFilterObject = (filterParams = {}) => {
  let filterObj = {};
    for (const key in filterParams) {
      if (filterParams.hasOwnProperty(key)) {
        if (isValidFilterFieldValue(filterParams[key])) {
          filterObj[key] = filterParams[key];
        }
      }
    }
  return filterObj;
}

// Check if filter filed has a valid value
export const isValidFilterFieldValue = (value) => {
  // also considering the fields having 'yes'/ 'no' option
  return value === 0 || boolean(value);
}

/**
 * column class like col-md-12, col-md-6
 * @param {String} layout Examle:- n*3 for col-md-4
 */
export const getColumnClass = (layout = 'n*1') => {
  const column_count = layout.split('*')[1];
  const col_partition = 12 / Number(column_count);
  return 'col-md-' + col_partition;
}