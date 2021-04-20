import { isValidFilterFieldValue, getColumnClass, getFilterObjectToArray, getValidFilterObject } from '../../../../FaveoReport/views/js/helpers/utils';


describe('isValidFilterFieldValue', () => {
  it('`isValidFilterFieldValue` will retrun result from `boolean(value)`, return true if value is 0', () => {
    expect(isValidFilterFieldValue(0)).toBe(true);
    expect(isValidFilterFieldValue(1)).toBe(true);
    expect(isValidFilterFieldValue(undefined)).toBe(false);
    expect(isValidFilterFieldValue([])).toBe(false);
  })
})

describe('getColumnClass', () => {
  it('should return column class for a column_coount', () => {
    expect(getColumnClass()).toBe('col-md-12');
    expect(getColumnClass('n*1')).toBe('col-md-12');
    expect(getColumnClass('n*3')).toBe('col-md-4');
  })
})

describe('getFilterObjectToArray', () => {
  it('should return array of object with key value pair', () => {
    expect(getFilterObjectToArray()).toStrictEqual([]);
    expect(getFilterObjectToArray({created_at: 'last::13~day'})).toStrictEqual([{key: 'created_at', value: 'last::13~day'}]);
    expect(getFilterObjectToArray({created_at: 'last::13~day', invalid_value: []})).toStrictEqual([{key: 'created_at', value: 'last::13~day'}]);
  })
})

describe('getValidFilterObject', () => {
  it('Should return only filter objects', () => {
    expect(getValidFilterObject()).toStrictEqual({});
    expect(getValidFilterObject({created_at: 'last::13~day', invalid_value: []})).toStrictEqual({created_at: 'last::13~day'});
  })
})
