import { mount } from '@vue/test-utils';
import TimeSeriesChart from './../../../views/js/components/Common/TimeSeriesChart';
import moxios from 'moxios';
import Vue from 'vue';
let wrapper;
window.eventHub = new Vue();

describe('TimeSeriesChart', () => {

  let mountComponent = (defaultCategory = 'view_by', categoryPrefix = 'view_by') => {
    wrapper = mount(TimeSeriesChart, {
      stubs: ['data-widget', 'faveo-chart', 'relative-loader', 'alert'],
      propsData: {
        chartDataApi: 'api/agent/top-customer-analysis',
        dataWidgetApi: undefined,
        categories: [],
        defaultCategory: defaultCategory,
        categoryPrefix: categoryPrefix,
        reportIndex: 1,
        filterParams: { testKey: 'testvalue' }
      },
      mocks: {
        lang: string => string
      }
    });
  }

  beforeEach(() => {
    moxios.install();
    mountComponent();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  it('prop default values', () => {
    expect(wrapper.props.dataWidgetApi).toBe(undefined);
    expect(wrapper.props.categories).toEqual(undefined);
  })

  it('assign defualt values while component init', () => {
    expect(wrapper.vm.selectedCategory).toBe('view_by');
  })


  it('assign value to `selectedCategory` when `onCategoryChange` called', () => {
    wrapper.vm.getDataFromServer = jest.fn();
    wrapper.vm.onCategoryChange('category');
    expect(wrapper.vm.selectedCategory).toBe('category');
    expect(wrapper.vm.getDataFromServer).toHaveBeenCalled();
  });

  it('getUrlParams will return url parameters with `view_by` and `filterParams` if `filterParams` is truthy', () => {
    expect(wrapper.vm.getUrlParams('iamtype').testKey).toEqual('testvalue');
  })

  it('calls api for chart when `getDataFromServer` called', (done) => {
    mockChartApi();
    wrapper.vm.getDataFromServer();
    setTimeout(() => {
      expect(moxios.requests.mostRecent().config.url).toBe('api/agent/top-customer-analysis');
      done();
    });
  })

  it('watch `filterParams` prop, if change detected call getDataFromServer function', () => {
    wrapper.vm.getDataFromServer = jest.fn();
    wrapper.setProps({filterParams: { id:1, name:'one' }});
    expect(wrapper.vm.getDataFromServer).toHaveBeenCalled();
  })
})

const chartApiData = [
  {
    "id": "received_tickets",
    "name": "received_tickets",
    "data": [
      {
        "id": 15,
        "label": "Test Org",
        "redirectTo": "http://localhost/test1",
        "value": 9
      },
      {
        "id": 14,
        "label": "Microprecision Solutions Pvt.Ltd",
        "redirectTo": "http://localhost/test2",
        "value": 14
      },
      {
        "id": 7,
        "label": "Ladybird Web Solution Pvt Ltd",
        "redirectTo": "http://localhost/test3",
        "value": 324
      },
      {
        "id": 9,
        "label": "Due North Yoga Pvt Ltd",
        "redirectTo": "http://localhost/test4",
        "value": 646
      }
    ],
    "dataLabel": "ticket_count"
  }
];

function mockChartApi() {
  moxios.stubRequest('api/agent/top-customer-analysis', {
    status: 200,
    response: { success: true, data: chartApiData },
  })
}
