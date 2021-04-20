/**
 * Common utilty for chart operations
 */

'use strict';

import { boolean } from 'helpers/extraLogics';


/**
* parse chart data
* @param {Array} `chartData`
* @returns {Object} `{series, labels, redirectURLs}`
*/
export const getCategoryBasedChartData = (chartData) => {
  let data = [];
  let labels = [];
  let redirectURLs = [];
  chartData.forEach((element) => {
    data.push(element.value);
    labels.push(element.label);
    redirectURLs.push(element.redirectTo);
  });

  return { data, labels, redirectURLs };
}

/**
 * 
 * @param {Array} apiData 
 * 
 * parse api response data for category based charts
 */
export const parseCategoryBasedChartApiData = (apiData) => {
  let chartArray = [];

  apiData.forEach(element => {
    const parsedData = getCategoryBasedChartData(element.data);
    let chartData = {
      chartId: element.id,
      panelTitle: element.name,
      axisLabel: element.dataLabel,
      data: parsedData.data,
      labels: parsedData.labels,
      redirectURLs: parsedData.redirectURLs
    }
    chartArray.push(chartData);
  });

  return chartArray;
}

/**
 * 
 * @param {Array} chartData
 * 
 * @return {Array} unique labels
 * 
 * Pick the labels from chart api data and return the array of labels
 */
export const getTimeLabels = (chartData) => {
  const chartApiData = JSON.parse(JSON.stringify(chartData));
  let labelsWithIds = [];
  for (let i = 0; i < chartApiData.length; i++) {
    for (let j = 0; j < chartApiData[i].data.length; j++) {
      const dataObj = chartApiData[i].data[j];
      const timestampObj = {
        id: dataObj.id,
        label: dataObj.label
      }
      labelsWithIds.push(timestampObj);
    }
  }

  const sortedList = labelsWithIds.sort(compareByDateOrNumber);

  const labels = getUniqueList(sortedList, 'label').map(item => { return item.label });

  return labels;

}

export const getUniqueList = (list, key) => {
  const uniqueList = Array.from(new Set(list.map(item => item[key])))
    .map(k => {
      return list.find(element => element[key] === k)
    })
  return uniqueList;
}


/**
 * Comparator for sorting array in ascending order
 */
const compareByDateOrNumber = (a, b) => {
  if(isNaN(Number(a.id))) {
    return new Date(a.id) - new Date(b.id);
  } else {
    return Number(a.id) - Number(b.id);
  }
}

function addTimeSeriesChartData(chartApiData, timeSeriesLabels, _chartData, i, j) {
  for (let k = 0; k < chartApiData[j].data.length + 1; k++) {
    if (k === chartApiData[j].data.length) {
      _chartData.values.push(0);
      _chartData.redirectTo.push(null);
    }
    else if (chartApiData[j].data[k].label === timeSeriesLabels[i]) {
      _chartData.values.push(chartApiData[j].data[k].value || 0);
      _chartData.redirectTo.push(chartApiData[j].data[k].redirectTo);
      break;
    }
  }
}

/**
 * 
 * @param {Array} chartData
 * @param {Array} timeSeriesLabels
 * 
 * Parse time series chart data
 */
export function parseTimeSeriesChartData(chartData, timeSeriesLabels) {
  const chartApiData = JSON.parse(JSON.stringify(chartData));
  let dataCollections = [];
  for (let j = 0; j < chartApiData.length; j++) {

    // Chart js data object
    let _chartData = {
      chartId: '',
      label: '',
      values: [],
      redirectTo: []
    };

    // Add chart name
    _chartData.label = chartApiData[j].name;
    // Add chart id
    _chartData.chartId = chartApiData[j].id;

    for (let i = 0; i < timeSeriesLabels.length; i++) {
      addTimeSeriesChartData(chartApiData, timeSeriesLabels, _chartData, i, j)
    }

    dataCollections.push(_chartData);
  }
  return dataCollections;
}

/**
 * Open url which is embeded in chart
 * 
 * @param {Array | undefined} event 
 * @param {Array | undefined} redirectURLs 
 */
export const redirectToURL = (event, redirectURLs) => {
  if (boolean(event) && boolean(redirectURLs)) {
    const redirectLink = redirectURLs[event[0]._index];
    if (boolean(redirectLink)) {
      open(redirectLink, '_blank');
    }
  }
}

/**
 * Truncate string to limit provided(default is 18)
 * Also ellipsify the text
 * 
 * @param {String} str 
 * @param {Number | undefined} stringLimit 
 */
export const truncateString = (str, stringLimit = 18) => {
  return (str.length > stringLimit) ? str.substr(0, stringLimit).trim() + '...' : str;
}

/**
 * get percentage value of the given dataset
 * @param {Number} value 
 * @param {Array} chartDataSet 
 */
export const getDatalabelInPercentage = (value, chartDataSet) => {
  const dotZeroZeroRegex = /\.00$/;
  const datasets = JSON.parse(JSON.stringify(chartDataSet.dataset.data));
  const totalSum = datasets.reduce((accumulator, currentValue) => Number(accumulator) + Number(currentValue));
  let percentageVal = ((Number(value) / totalSum) * 100).toFixed(2) + '';
  if (dotZeroZeroRegex.test(percentageVal)) {
    percentageVal = percentageVal.replace(dotZeroZeroRegex, '');
  }
  return percentageVal + '%';
}

export const hoverHandler = (items, chartId, redirectUrls) => {
  try {
    const el = document.getElementById(chartId);
    el.style.cursor = 'default';
    if(items[0] && redirectUrls[items[0]._index]) {
      el.style.cursor = 'pointer';
    }
  } catch (error) {
    // Do nothing
  }
}
