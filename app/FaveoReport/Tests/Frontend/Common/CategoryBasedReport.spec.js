import { mount } from '@vue/test-utils';
import CategoryBasedReport from './../../../views/js/components/Common/CategoryBasedReport';
import moxios from 'moxios';
import Vue from 'vue';
let wrapper;
window.eventHub = new Vue();

describe('CategoryBasedReport', () => {

  let mountComponent = (categories = [3, 5, 10], defaultCategory = 5, categoryPrefix = 'top_n') => {
    wrapper = mount(CategoryBasedReport, {
      stubs: ['ticket-filter', 'faveo-chart', 'loader', 'alert', 'data-widget'],
      propsData: {
        categoryChartDataApi: 'api/agent/top-customer-analysis',
        chartType: 'bar',
        categories: categories,
        defaultCategory: defaultCategory,
        categoryPrefix: categoryPrefix,
        filterParams: { key: 'testvalue' },
        reportPanelTitle: 'reportPanelTitle',
        widgetDataApi: undefined,
        layoutClass: 'col-md-12',
        reportIndex: 1
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

  it('assign defualt values while component init', () => {
    expect(wrapper.vm.selectedCategory).toBe(5);
  })

  it('getUrlParams will return url parameters with `top_n` if `filterParams` is falsy and reportType is topCustomerAnalysis', () => {
    wrapper.vm.selectedTopN = 5,
      expect(wrapper.vm.getUrlParams('iamtype').top_n).toEqual(5);
  })

  it('assign value to `selectedCategory` when `onCategoryChange` called', () => {
    wrapper.vm.getDataFromServer = jest.fn();
    wrapper.vm.onCategoryChange('category');
    expect(wrapper.vm.selectedCategory).toBe('category');
    expect(wrapper.vm.getDataFromServer).toHaveBeenCalled();
  });

  it('getUrlParams will return url parameters with `top_n` and `filterParams` if `filterParams` is truthy', () => {
    expect(wrapper.vm.getUrlParams('iamtype').key).toEqual('testvalue');
  })

  it('calls api for chart when `getDataFromServer` called', (done) => {
    mockChartApi();
    wrapper.vm.getDataFromServer();
    setTimeout(() => {
      expect(moxios.requests.mostRecent().config.url).toBe('api/agent/top-customer-analysis');
      done();
    });
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
