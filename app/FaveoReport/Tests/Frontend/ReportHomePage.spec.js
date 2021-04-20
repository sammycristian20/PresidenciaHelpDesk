import { mount } from '@vue/test-utils';
import ReportHomePage from './../../views/js/components/ReportHomePage.vue';
import moxios from 'moxios';
import Vue from 'vue';
import responseHandler from 'helpers/responseHandler';
import Vuex from 'vuex'

let wrapper;
window.eventHub = new Vue();

Vue.use(Vuex);

global.confirm = jest.fn(() => {
  return true;
})

let store;

let getters = {
  formattedTime: () => () => {return ''},
}

store = new Vuex.Store({
  getters
})

jest.mock('helpers/responseHandler', () => ({
  errorHandler: ()=> jest.fn(),
  successHandler: ()=> jest.fn()
}));


describe('ReportHomePage', () => {

  let mountComponent = () => {
    wrapper = mount(ReportHomePage, {
      stubs: ['custom-loader', 'alert'],
      mocks: {
        lang: string => string
      },
      store
    });
  }

  beforeEach(() => {
    moxios.install();
    mountComponent();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  it('getReportList will get report list array -- SUCCESS', function (done) {

    wrapper.vm.updateFilterObj = jest.fn();

    wrapper.vm.getReportList();

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 200,
        response: { data: [{id: 0, name: 'report_1'}, {id: 1, name: 'report_2'}] }
      }).then(function () {
        expect(request.config.url).toBe('api/agent/report-list')
        expect(wrapper.vm.reportList).toStrictEqual([{id: 0, name: 'report_1'}, {id: 1, name: 'report_2'}]);
        expect(wrapper.vm.isLoading).toBe(false);
        done();
      })
    })
  })

  it('getReportList will get report list array -- ERROR', function (done) {

    responseHandler.errorHandler = jest.fn();

    wrapper.vm.getReportList();

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 400,
        response: { data: [{id: 0, name: 'report_1'}, {id: 1, name: 'report_2'}] }
      }).then(function () {
        expect(request.config.url).toBe('api/agent/report-list');
        expect(wrapper.vm.isLoading).toBe(false);
        expect(responseHandler.errorHandler).toHaveBeenCalled();
        done();
      })
    })
  })


  it('deleteCustomReport will delete custom report from the list -- SUCCESS', function (done) {

    wrapper.vm.updateFilterObj = jest.fn();

    wrapper.vm.deleteCustomReport(3);

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 200,
        response: { message: '' }
      }).then(function () {
        expect(request.config.url).toBe('api/report/3')
        done();
      })
    })
  })

  it('deleteCustomReport will delete custom report from the list -- ERROR', function (done) {

    responseHandler.errorHandler = jest.fn();

    wrapper.vm.deleteCustomReport(3);

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 400,
        response: { message: '' }
      }).then(function () {
        expect(request.config.url).toBe('api/report/3');
        expect(wrapper.vm.isLoading).toBe(false);
        expect(responseHandler.errorHandler).toHaveBeenCalled();
        done();
      })
    })
  })

})